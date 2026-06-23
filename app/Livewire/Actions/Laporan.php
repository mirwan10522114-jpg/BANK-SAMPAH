<?php

namespace App\Livewire\Actions;

use Livewire\Component;

class Laporan extends Component
{
    public function render()
    {
        return view('pages.admin.koperasi.laporan')
            ->layout('layouts.app', ['title' => __('Laporan Koperasi')]);
    }
}