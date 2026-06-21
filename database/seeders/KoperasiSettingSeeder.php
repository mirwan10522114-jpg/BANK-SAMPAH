<?php

namespace Database\Seeders;

use App\Models\KoperasiSetting;
use Illuminate\Database\Seeder;

class KoperasiSettingSeeder extends Seeder
{
    /**
     * Isi 1 baris pengaturan default koperasi.
     * Aman dijalankan berkali-kali (pakai updateOrCreate, tidak akan duplikat).
     */
    public function run(): void
    {
        KoperasiSetting::updateOrCreate(
            ['id' => 1],
            [
                'nama_koperasi' => 'Koperasi Simpan Pinjam Bank Sampah Sukamaju Sejahtera',
                'telepon' => null,
                'email' => null,
                'alamat' => null,
                'logo' => null,
                'nominal_simpanan_pokok' => 50000,
                'nominal_simpanan_wajib' => 10000,
                'biaya_admin_pinjaman' => 0,
                'saldo_kas_awal' => 0,
                'tanggal_saldo_awal' => now()->toDateString(),
            ]
        );
    }
}
