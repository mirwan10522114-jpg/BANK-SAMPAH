<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KoperasiAnggotaKeluar extends Model
{
    protected $table = 'koperasi_anggota_keluars';

    protected $fillable = [
        'koperasi_anggota_id',
        'total_simpanan',
        'sisa_pinjaman',
        'dana_dikembalikan',
        'tanggal_keluar',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'total_simpanan' => 'decimal:2',
        'sisa_pinjaman' => 'decimal:2',
        'dana_dikembalikan' => 'decimal:2',
        'tanggal_keluar' => 'date',
    ];

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(KoperasiAnggota::class, 'koperasi_anggota_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
