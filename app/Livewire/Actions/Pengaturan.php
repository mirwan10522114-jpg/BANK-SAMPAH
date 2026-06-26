<?php

namespace App\Livewire\Actions;

<<<<<<< HEAD
use App\Models\KoperasiSetting;
use Livewire\Component;
=======
use Livewire\Component;
use App\Models\KoperasiSetting;
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
use Mary\Traits\Toast;

class Pengaturan extends Component
{
    use Toast;

<<<<<<< HEAD
    // Field form — nama mengikuti kolom yang benar di tabel koperasi_settings.
    public $nominal_simpanan_pokok;

    public $nominal_simpanan_wajib;

    public $biaya_admin_pinjaman;

    public $saldo_kas_awal;

    public $tanggal_saldo_awal;

    public function mount()
    {
        // Ambil baris pengaturan (singleton). current() menjamin selalu ada.
        $setting = KoperasiSetting::current();

        $this->nominal_simpanan_pokok = $setting->nominal_simpanan_pokok ?? 0;
        $this->nominal_simpanan_wajib = $setting->nominal_simpanan_wajib ?? 0;
        $this->biaya_admin_pinjaman = $setting->biaya_admin_pinjaman ?? 0;
        $this->saldo_kas_awal = $setting->saldo_kas_awal ?? 0;
        $this->tanggal_saldo_awal = $setting->tanggal_saldo_awal?->format('Y-m-d');
=======
    public $simpanan_pokok;
    public $bunga_pinjaman;
    public $saldo_kas_awal;

    public function mount()
    {
        // Ambil data pengaturan pertama dari database (jika ada)
        $setting = KoperasiSetting::first();

        if ($setting) {
            // Cek nama kolom yang tersedia (mengakomodasi variasi nama kolom di database Anda)
            $this->simpanan_pokok = $setting->simpanan_pokok ?? $setting->nominal_simpanan_pokok ?? 50000;
            $this->bunga_pinjaman = $setting->bunga_pinjaman ?? 12;
            $this->saldo_kas_awal = $setting->saldo_kas_awal ?? 1000000;
        } else {
            // Nilai default jika database masih benar-benar kosong
            $this->simpanan_pokok = 50000;
            $this->bunga_pinjaman = 12;
            $this->saldo_kas_awal = 1000000;
        }
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
    }

    public function simpanPengaturan()
    {
        $this->validate([
<<<<<<< HEAD
            'nominal_simpanan_pokok' => 'required|numeric|min:0',
            'nominal_simpanan_wajib' => 'required|numeric|min:0',
            'biaya_admin_pinjaman' => 'required|numeric|min:0',
            'saldo_kas_awal' => 'required|numeric|min:0',
            'tanggal_saldo_awal' => 'nullable|date',
        ]);

        // Simpan/perbarui baris pengaturan (hanya 1 baris / singleton).
        KoperasiSetting::current()->update([
            'nominal_simpanan_pokok' => $this->nominal_simpanan_pokok,
            'nominal_simpanan_wajib' => $this->nominal_simpanan_wajib,
            'biaya_admin_pinjaman' => $this->biaya_admin_pinjaman,
            'saldo_kas_awal' => $this->saldo_kas_awal,
            'tanggal_saldo_awal' => $this->tanggal_saldo_awal ?: null,
        ]);
=======
            'simpanan_pokok' => 'required|numeric|min:0',
            'bunga_pinjaman' => 'required|numeric|min:0|max:100',
            'saldo_kas_awal' => 'required|numeric|min:0',
        ]);

        // Simpan atau perbarui pengaturan (memastikan hanya ada 1 baris pengaturan di database)
        KoperasiSetting::updateOrCreate(
            ['id' => 1], // Selalu update baris pertama
            [
                'simpanan_pokok' => $this->simpanan_pokok,
                'nominal_simpanan_pokok' => $this->simpanan_pokok, // Mengisi 2 field agar aman
                'bunga_pinjaman' => $this->bunga_pinjaman,
                'saldo_kas_awal' => $this->saldo_kas_awal,
            ]
        );
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea

        // Munculkan notifikasi sukses hijau
        $this->success('Pengaturan koperasi berhasil disimpan dan diperbarui.');
    }

    public function render()
    {
        return view('pages.admin.koperasi.pengaturan')
            ->layout('layouts.app', ['title' => __('Pengaturan Koperasi')]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
