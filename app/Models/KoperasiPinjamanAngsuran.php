<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KoperasiPinjamanAngsuran extends Model
{
    protected $table = 'koperasi_pinjaman_angsurans';

    protected $fillable = [
        'koperasi_pinjaman_id',
        'angsuran_ke',
        'jumlah_bayar',
        'tanggal_bayar',
        'sisa_pinjaman_setelah',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'sisa_pinjaman_setelah' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(KoperasiPinjaman::class, 'koperasi_pinjaman_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
