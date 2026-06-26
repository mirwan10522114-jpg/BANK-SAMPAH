<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KoperasiSetting extends Model
{
    protected $table = 'koperasi_settings';

    protected $fillable = [
        'nama_koperasi',
        'telepon',
        'email',
        'alamat',
        'logo',
        'nominal_simpanan_pokok',
        'nominal_simpanan_wajib',
        'biaya_admin_pinjaman',
        'saldo_kas_awal',
        'tanggal_saldo_awal',
    ];

    protected $casts = [
        'nominal_simpanan_pokok' => 'decimal:2',
        'nominal_simpanan_wajib' => 'decimal:2',
        'biaya_admin_pinjaman' => 'decimal:2',
        'saldo_kas_awal' => 'decimal:2',
        'tanggal_saldo_awal' => 'date',
    ];

    /**
     * Helper untuk ambil baris pengaturan (selalu hanya 1 baris / singleton).
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], ['nama_koperasi' => 'Koperasi Simpan Pinjam']);
    }
}
