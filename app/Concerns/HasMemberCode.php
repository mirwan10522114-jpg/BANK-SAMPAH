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
<<<<<<< HEAD
            if (! in_array(UserRole::Nasabah->value, $model->roles ?? [], strict: true)) {
=======
            if ($model->role !== UserRole::Nasabah) {
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
                return;
            }

            if (empty($model->member_code)) {
                $model->member_code = MemberCodeCounter::generate('BS', 3);
            }
        });
    }
}