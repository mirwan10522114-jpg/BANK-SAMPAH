<?php

namespace App\Livewire\Actions;

use Livewire\Component;
use App\Models\KoperasiSetting;
use Mary\Traits\Toast;

class Pengaturan extends Component
{
    use Toast;

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
    }

    public function simpanPengaturan()
    {
        $this->validate([
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

        // Munculkan notifikasi sukses hijau
        $this->success('Pengaturan koperasi berhasil disimpan dan diperbarui.');
    }

    public function render()
    {
        return view('pages.admin.koperasi.pengaturan')
            ->layout('layouts.app', ['title' => __('Pengaturan Koperasi')]);
    }
}