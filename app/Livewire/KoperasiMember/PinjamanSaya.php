<?php

namespace App\Livewire\KoperasiMember;

use App\Models\KoperasiAnggota;
use Livewire\Component;

class PinjamanSaya extends Component
{
    public ?KoperasiAnggota $anggota = null;

    public function mount(): void
    {
        $this->anggota = auth()->user()->koperasiAnggota;
    }

    public function render()
    {
        $pinjamans = $this->anggota
            ? $this->anggota->pinjamans()->with('angsurans')->latest('tanggal_pengajuan')->get()
            : collect();

        return view('pages.koperasi-member.pinjaman-saya', compact('pinjamans'))
            ->layout('layouts.app', ['title' => 'Pinjaman Saya']);
    }
}
