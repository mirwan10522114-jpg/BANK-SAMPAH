<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\UserRole;
use App\Models\MemberCodeCounter;

trait HasMemberCode
{
    protected static function bootHasMemberCode(): void
    {
        static::creating(function ($model): void {
            if ($model->role !== UserRole::Nasabah) {
                return;
            }

            if (empty($model->member_code)) {
                $model->member_code = MemberCodeCounter::generate('BS', 3);
            }
        });
    }
}