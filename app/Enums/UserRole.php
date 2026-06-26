<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Owner = 'owner';
    case Nasabah = 'nasabah';
<<<<<<< HEAD
    case Koperasi = 'koperasi';
=======
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Owner => 'Owner',
            self::Nasabah => 'Nasabah',
<<<<<<< HEAD
            self::Koperasi => 'Koperasi',
=======
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
        };
    }
}
