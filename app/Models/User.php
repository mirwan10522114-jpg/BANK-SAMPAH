<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use App\Concerns\HasMemberCode;
use App\Services\EmailOtpService;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'roles', 'phone', 'address', 'is_member', 'member_joined_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasMemberCode, Notifiable;

    /**
     * Replace Laravel's link-based email verification with our OTP flow.
     * Triggered via the Registered event listener + manual resend endpoint.
     */
    public function sendEmailVerificationNotification(): void
    {
        try {
            app(EmailOtpService::class)->send($this->email, EmailOtp::PURPOSE_EMAIL_VERIFICATION);
        } catch (\RuntimeException) {
            // Cooldown active — user can manually resend from the verify page.
        }
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'roles' => 'array',
            'is_member' => 'boolean',
            'member_joined_at' => 'date',
        ];
    }

    public function scopeNasabah(Builder $query): Builder
    {
        return $query->whereJsonContains('roles', UserRole::Nasabah->value);
    }

    public function hasRole(string|UserRole $role): bool
    {
        $value = $role instanceof UserRole ? $role->value : $role;

        return in_array($value, $this->roles ?? [], strict: true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin);
    }

    public function isOwner(): bool
    {
        return $this->hasRole(UserRole::Owner);
    }

    public function isNasabah(): bool
    {
        return $this->hasRole(UserRole::Nasabah);
    }

    public function isKoperasi(): bool
    {
        return $this->hasRole(UserRole::Koperasi);
    }

    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isOwner();
    }

    public function koperasiAnggota(): HasOne
    {
        return $this->hasOne(KoperasiAnggota::class);
    }

    public function balance(): HasOne
    {
        return $this->hasOne(Balance::class);
    }

    public function savingTransactions(): HasMany
    {
        return $this->hasMany(SavingTransaction::class);
    }

    public function balanceHistories(): HasMany
    {
        return $this->hasMany(BalanceHistory::class);
    }

    public function pointHistories(): HasMany
    {
        return $this->hasMany(PointHistory::class);
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
