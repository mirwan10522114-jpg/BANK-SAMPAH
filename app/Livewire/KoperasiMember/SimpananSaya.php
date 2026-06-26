<?php

namespace App\Livewire\KoperasiMember;

use App\Models\KoperasiAnggota;
use App\Models\KoperasiSimpananTransaksi;
use Livewire\Component;

class SimpananSaya extends Component
{
    public ?KoperasiAnggota $anggota = null;

    public function mount(): void
    {
        $this->anggota = auth()->user()->koperasiAnggota;
    }

    public function render()
    {
        $saldos = $this->anggota
            ? $this->anggota->simpananSaldos()->get()
            : collect();

        $transaksis = $this->anggota
            ? KoperasiSimpananTransaksi::where('koperasi_anggota_id', $this->anggota->id)
                ->orderBy('tanggal_transaksi', 'desc')
                ->get()
            : collect();

        return view('pages.koperasi-member.simpanan-saya', compact('saldos', 'transaksis'))
            ->layout('layouts.app', ['title' => 'Simpanan Saya']);
    }
}