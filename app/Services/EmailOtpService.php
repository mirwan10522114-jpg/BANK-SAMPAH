<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\EmailOtpMail;
use App\Models\EmailOtp;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use RuntimeException;

/**
 * Manages email-based OTP codes for password reset and email verification.
 *
 * OTP codes are 6-digit numeric, hashed in DB, TTL 10 minutes, 5 attempt cap.
 * A 60-second cooldown between issues throttles abuse.
 *
 * Defense-in-depth rate limiting (on top of the per-code rules above):
 *  - SEND  is throttled per email AND per IP, to stop mail-bombing across
 *          many addresses from a single attacker.
 *  - VERIFY is throttled per (email, purpose, IP), to cap total guesses an
 *          attacker can make even after requesting fresh codes repeatedly.
 */
class EmailOtpService
{
    /** Max OTP issuances per email within the send window. */
    private const SEND_PER_EMAIL = 3;

    /** Max OTP issuances per IP within the send window. */
    private const SEND_PER_IP = 10;

    /** Verify rate-limit window in minutes. */
    private const VERIFY_WINDOW_MINUTES = 10;

    /** Max verify attempts per (email, purpose, IP) within the window. */
    private const VERIFY_MAX_ATTEMPTS = 15;

    /** Rate-limit window for send throttling, in minutes. */
    private const SEND_WINDOW_MINUTES = 10;

    public function __construct(
        private readonly RateLimiter $limiter,
    ) {}

    /**
     * Generate and email a new OTP for the given email + purpose.
     * Invalidates any existing unused OTPs for the same pair.
     */
    public function send(string $email, string $purpose): EmailOtp
    {
        $this->assertPurpose($purpose);

        $email = strtolower(trim($email));
        $ip = $this->clientIp();

        // Throttle issuance per email and per IP — independent caps.
        $this->assertNotTooMany('otp:send:email:'.$email, self::SEND_PER_EMAIL, self::SEND_WINDOW_MINUTES * 60);
        $this->assertNotTooMany('otp:send:ip:'.$ip, self::SEND_PER_IP, self::SEND_WINDOW_MINUTES * 60);

        $recent = EmailOtp::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->where('created_at', '>=', now()->subSeconds(EmailOtp::COOLDOWN_SECONDS))
            ->latest('id')
            ->first();

        if ($recent) {
            $wait = max(1, EmailOtp::COOLDOWN_SECONDS - now()->diffInSeconds($recent->created_at));

            throw new RuntimeException("Tunggu {$wait} detik sebelum meminta kode baru.");
        }

        // Invalidate older unused OTPs for same (email, purpose).
        EmailOtp::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $code = $this->generateCode();

        $otp = EmailOtp::create([
            'email' => $email,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(EmailOtp::TTL_MINUTES),
            'created_at' => now(),
        ]);

        // Only count a successful issuance toward the rate limit.
        $this->limiter->hit('otp:send:email:'.$email, self::SEND_WINDOW_MINUTES * 60);
        $this->limiter->hit('otp:send:ip:'.$ip, self::SEND_WINDOW_MINUTES * 60);

        Mail::to($email)->send(new EmailOtpMail($code, $purpose));

        return $otp;
    }

    /**
     * Verify an OTP. On success, marks it used. On failure, increments attempts.
     * Throws InvalidArgumentException with a user-friendly message on failure.
     */
    public function verify(string $email, string $purpose, string $code): EmailOtp
    {
        $this->assertPurpose($purpose);

        $email = strtolower(trim($email));
        $code = trim($code);
        $ip = $this->clientIp();

        // Throttle brute-force attempts per (email, purpose, IP). This caps the
        // total guesses across multiple freshly-issued codes.
        $verifyKey = 'otp:verify:'.$email.':'.$purpose.':'.$ip;
        $this->assertNotTooMany($verifyKey, self::VERIFY_MAX_ATTEMPTS, self::VERIFY_WINDOW_MINUTES * 60);

        if (! preg_match('/^\d{6}$/', $code)) {
            $this->limiter->hit($verifyKey, self::VERIFY_WINDOW_MINUTES * 60);
            throw new InvalidArgumentException('Kode harus 6 digit angka.');
        }

        $otp = EmailOtp::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->latest('id')
            ->first();

        if (! $otp) {
            $this->limiter->hit($verifyKey, self::VERIFY_WINDOW_MINUTES * 60);
            throw new InvalidArgumentException('Kode tidak ditemukan. Minta kode baru.');
        }

        if ($otp->expires_at->isPast()) {
            throw new InvalidArgumentException('Kode sudah kadaluarsa. Minta kode baru.');
        }

        if ($otp->attempts >= EmailOtp::MAX_ATTEMPTS) {
            throw new InvalidArgumentException('Percobaan melebihi batas. Minta kode baru.');
        }

        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');
            $this->limiter->hit($verifyKey, self::VERIFY_WINDOW_MINUTES * 60);

            $remaining = EmailOtp::MAX_ATTEMPTS - $otp->attempts;

            throw new InvalidArgumentException("Kode salah. Sisa percobaan: {$remaining}.");
        }

        $otp->used_at = now();
        $otp->save();

        // Clear the verify limiter on success so a legitimate user isn't penalized.
        $this->limiter->clear($verifyKey);

        return $otp;
    }

    /**
     * Throw a friendly error when the named rate-limiter key is exhausted.
     */
    private function assertNotTooMany(string $key, int $max, int $decaySeconds): void
    {
        if ($this->limiter->tooManyAttempts($key, $max)) {
            $seconds = $this->limiter->availableIn($key);
            $wait = max(1, (int) ceil($seconds / 60));

            throw new RuntimeException("Terlalu banyak percobaan. Coba lagi dalam {$wait} menit.");
        }
    }

    private function clientIp(): string
    {
        return request()?->ip() ?? '127.0.0.1';
    }

    private function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function assertPurpose(string $purpose): void
    {
        if (! in_array($purpose, [
            EmailOtp::PURPOSE_PASSWORD_RESET,
            EmailOtp::PURPOSE_EMAIL_VERIFICATION,
        ], true)) {
            throw new InvalidArgumentException("Purpose OTP tidak valid: '{$purpose}'.");
        }
    }
}
