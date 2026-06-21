<?php

namespace App\Livewire\Admin\Koperasi;

use Livewire\Component;

class Laporan extends Component
{
    public function render()
    {
        return view('livewire.admin.koperasi.laporan')
            ->layout('layouts.app', ['title' => __('Laporan Koperasi')]);
    }
}
