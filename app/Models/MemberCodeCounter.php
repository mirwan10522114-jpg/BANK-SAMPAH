<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Atomically issues sequential member codes (e.g. BS001, BS002).
 *
 * The counter row is locked inside a DB transaction so concurrent nasabah
 * registrations never collide on the same number. An explicit row-level
 * lock (`lockForUpdate`) + the unique `prefix` column make generation safe
 * under load; a retry layer guards the rare unique-key collision.
 */
class MemberCodeCounter extends Model
{
    /** Maximum attempts when a unique collision is hit during generation. */
    private const GENERATE_MAX_ATTEMPTS = 5;

    protected $fillable = [
        'prefix',
        'last_number',
    ];

    protected $casts = [
        'last_number' => 'integer',
    ];

    public $timestamps = true;

    /**
     * Atomically generate the next member code for the given prefix.
     *
     * @param  string  $prefix  Code prefix, e.g. 'BS'.
     * @param  int  $pad  Number of digits to zero-pad the counter to.
     * @return string The formatted code, e.g. 'BS001'.
     *
     * @throws RuntimeException When generation fails after several retries
     *                          (only happens under pathological contention).
     */
    public static function generate(string $prefix, int $pad = 3): string
    {
        $prefix = strtoupper(trim($prefix));

        for ($attempt = 1; $attempt <= self::GENERATE_MAX_ATTEMPTS; $attempt++) {
            try {
                return DB::transaction(function () use ($prefix, $pad): string {
                    // lockForUpdate serializes concurrent generators on the
                    // same prefix row (creates it first if missing).
                    $counter = static::where('prefix', $prefix)
                        ->lockForUpdate()
                        ->first();

                    if (! $counter) {
                        // First time for this prefix — create with last_number = 1.
                        // Use insert + lock to avoid a race between two creators;
                        // the unique(prefix) constraint makes only one win.
                        try {
                            $counter = static::create([
                                'prefix' => $prefix,
                                'last_number' => 1,
                            ]);
                        } catch (QueryException $e) {
                            // Another process created it first; re-fetch under lock.
                            $counter = static::where('prefix', $prefix)
                                ->lockForUpdate()
                                ->first();

                            if (! $counter) {
                                throw $e;
                            }

                            $counter->last_number += 1;
                            $counter->save();
                        }
                    } else {
                        $counter->last_number += 1;
                        $counter->save();
                    }

                    return $prefix.str_pad((string) $counter->last_number, $pad, '0', STR_PAD_LEFT);
                });
            } catch (QueryException $e) {
                // Retry only on unique-constraint violations (concurrent insert
                // on a fresh prefix). Any other error must bubble up.
                if ($attempt === self::GENERATE_MAX_ATTEMPTS || ! str_contains((string) $e->getMessage(), 'Duplicate')) {
                    throw $e;
                }

                // Brief backoff before retrying.
                usleep(5_000 * $attempt);
            }
        }

        // Unreachable: the loop always returns or throws above.
        throw new RuntimeException("Gagal membuat member code untuk prefix '{$prefix}'.");
    }
}
