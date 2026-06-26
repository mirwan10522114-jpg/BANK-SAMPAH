<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KoperasiPinjaman extends Model
{
    protected $table = 'koperasi_pinjamans';

    protected $fillable = [
        'nomor_pinjaman',
        'koperasi_anggota_id',
        'jumlah_pinjaman',
        'tenor_bulan',
        'angsuran_per_bulan',
        'biaya_admin',
        'tanggal_pengajuan',
        'tanggal_pencairan',
        'status',
        'sisa_pinjaman',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'jumlah_pinjaman' => 'decimal:2',
        'angsuran_per_bulan' => 'decimal:2',
        'biaya_admin' => 'decimal:2',
        'sisa_pinjaman' => 'decimal:2',
        'tanggal_pengajuan' => 'date',
        'tanggal_pencairan' => 'date',
    ];

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(KoperasiAnggota::class, 'koperasi_anggota_id');
    }

    public function angsurans(): HasMany
    {
        return $this->hasMany(KoperasiPinjamanAngsuran::class, 'koperasi_pinjaman_id')
            ->orderBy('angsuran_ke');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Tanpa bunga: angsuran tetap = jumlah_pinjaman / tenor_bulan.
     */
    public static function hitungAngsuranPerBulan(float $jumlahPinjaman, int $tenorBulan): float
    {
        return round($jumlahPinjaman / max($tenorBulan, 1), 2);
    }

    /**
     * Nomor urut angsuran berikutnya (angsuran ke berapa).
     */
    public function angsuranKeBerikutnya(): int
    {
        return ($this->angsurans()->max('angsuran_ke') ?? 0) + 1;
    }
}
