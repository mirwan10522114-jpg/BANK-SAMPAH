<?php

namespace App\Livewire\Actions;

use App\Models\KoperasiSetting;
use Livewire\Component;
use Mary\Traits\Toast;

class Pengaturan extends Component
{
    use Toast;

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
    }

    public function simpanPengaturan()
    {
        $this->validate([
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

        // Munculkan notifikasi sukses hijau
        $this->success('Pengaturan koperasi berhasil disimpan dan diperbarui.');
    }

    public function render()
    {
        return view('pages.admin.koperasi.pengaturan')
            ->layout('layouts.app', ['title' => __('Pengaturan Koperasi')]);
    }
}
