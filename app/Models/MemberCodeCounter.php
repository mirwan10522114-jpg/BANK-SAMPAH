<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MemberCodeCounter extends Model
{
    protected $fillable = [
        'prefix',
        'last_number',
    ];

    public static function nextNumber(string $prefix): int
    {
        return DB::transaction(function () use ($prefix) {
            $counter = self::query()
                ->lockForUpdate()
                ->firstOrCreate(
                    ['prefix' => $prefix],
                    ['last_number' => 0]
                );

            $counter->increment('last_number');

            return $counter->last_number;
        });
    }

    public static function generate(string $prefix, int $padLength = 3): string
    {
        $number = self::nextNumber($prefix);

        return $prefix.str_pad((string) $number, $padLength, '0', STR_PAD_LEFT);
    }
}