<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KoperasiSimpananSaldo extends Model
{
    protected $table = 'koperasi_simpanan_saldos';

    protected $fillable = [
        'koperasi_anggota_id',
        'jenis_simpanan',
        'saldo',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(KoperasiAnggota::class, 'koperasi_anggota_id');
    }
}
