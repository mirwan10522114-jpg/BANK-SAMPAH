<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KoperasiKasTransaksi extends Model
{
    protected $table = 'koperasi_kas_transaksis';

    protected $fillable = [
        'nomor_referensi',
        'sumber',
        'tipe',
        'jumlah',
        'keterangan',
        'tanggal_transaksi',
        'user_id',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_transaksi' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Total Kas (Liquid) saat ini = saldo_kas_awal + total masuk - total keluar.
     * Bisa difilter periode untuk dashboard / Mutasi Kas.
     */
    public static function saldoKasSaatIni(?string $sampaiTanggal = null): float
    {
        $query = static::query();

        if ($sampaiTanggal) {
            $query->where('tanggal_transaksi', '<=', $sampaiTanggal);
        }

        $masuk = $query->clone()->where('tipe', 'masuk')->sum('jumlah');
        $keluar = $query->clone()->where('tipe', 'keluar')->sum('jumlah');

        $saldoAwal = (float) KoperasiSetting::current()->saldo_kas_awal;

        return $saldoAwal + (float) $masuk - (float) $keluar;
    }
}
