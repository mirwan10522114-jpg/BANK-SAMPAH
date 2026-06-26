<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KoperasiSimpananTransaksi extends Model
{
    protected $table = 'koperasi_simpanan_transaksis';

    protected $fillable = [
        'nomor_transaksi',
        'koperasi_anggota_id',
        'jenis_simpanan',
        'tipe',
        'jumlah',
        'saldo_sebelum',
        'saldo_sesudah',
        'keterangan',
        'tanggal_transaksi',
        'user_id',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
        'tanggal_transaksi' => 'datetime',
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
