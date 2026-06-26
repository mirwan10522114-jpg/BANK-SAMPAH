<?php

namespace App\Livewire\KoperasiMember;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KoperasiSimpananTransaksi; 
// Pastikan model yang di-use sesuai dengan yang ada di file kamu

class SimpananSaya extends Component
{
    use WithPagination;

    // Properti untuk filter tanggal
    public $tanggal_mulai;
    public $tanggal_akhir;

    // Reset pagination ketika filter berubah
    public function updated($property)
    {
        if ($property === 'tanggal_mulai' || $property === 'tanggal_akhir') {
            $this->resetPage();
        }
    }

    public function render()
    {
        // Ambil data transaksi khusus untuk user yang sedang login
        $query = KoperasiSimpananTransaksi::query()
            ->whereHas('koperasiAnggota', function($q) {
                // Sesuaikan relasi 'koperasiAnggota' jika namanya berbeda di modelmu
                $q->where('user_id', auth()->id());
            });

        // Terapkan filter jika 'tanggal_mulai' diisi
        if ($this->tanggal_mulai) {
            $query->whereDate('created_at', '>=', $this->tanggal_mulai);
        }

        // Terapkan filter jika 'tanggal_akhir' diisi
        if ($this->tanggal_akhir) {
            $query->whereDate('created_at', '<=', $this->tanggal_akhir);
        }

        // Tampilkan semua (diurutkan dari terbaru), gunakan paginate agar rapi
        $transaksi = $query->latest()->paginate(10);

        return view('pages.koperasi-member.simpanan-saya', [
            'transaksi' => $transaksi
        ]);
    }
}