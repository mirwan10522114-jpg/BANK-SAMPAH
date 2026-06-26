<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\BelongsTo;
=======
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KoperasiAnggota extends Model
{
    use SoftDeletes;

    protected $table = 'koperasi_anggota';

    protected $fillable = [
<<<<<<< HEAD
        'user_id',
=======
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
        'nomor_anggota',
        'nama',
        'no_ktp',
        'no_telepon',
        'alamat',
        'foto',
        'status',
        'tanggal_bergabung',
        'tanggal_keluar',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'tanggal_keluar' => 'date',
    ];

<<<<<<< HEAD
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

=======
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
    public function simpananSaldos(): HasMany
    {
        return $this->hasMany(KoperasiSimpananSaldo::class, 'koperasi_anggota_id');
    }

    public function simpananTransaksis(): HasMany
    {
        return $this->hasMany(KoperasiSimpananTransaksi::class, 'koperasi_anggota_id');
    }

    public function pinjamans(): HasMany
    {
        return $this->hasMany(KoperasiPinjaman::class, 'koperasi_anggota_id');
    }

    public function riwayatKeluar(): HasOne
    {
        return $this->hasOne(KoperasiAnggotaKeluar::class, 'koperasi_anggota_id');
    }

    /**
     * Total seluruh jenis simpanan (pokok + wajib + sukarela) anggota ini.
     */
    public function getTotalSimpananAttribute(): float
    {
        return (float) $this->simpananSaldos()->sum('saldo');
    }

    /**
     * Saldo simpanan sukarela saja (satu-satunya yang bebas ditarik).
     */
    public function getSaldoSukarelaAttribute(): float
    {
        return (float) $this->simpananSaldos()
            ->where('jenis_simpanan', 'sukarela')
            ->value('saldo') ?? 0;
    }

    /**
     * Total sisa pinjaman dari semua pinjaman yang masih berjalan.
     */
    public function getSisaPinjamanAttribute(): float
    {
        return (float) $this->pinjamans()
            ->where('status', 'berjalan')
            ->sum('sisa_pinjaman');
    }
}
