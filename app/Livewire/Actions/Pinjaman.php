<?php

namespace App\Livewire\Admin\Koperasi;

use Livewire\Component;

class Pinjaman extends Component
{
    public function render()
    {
        return view('livewire.admin.koperasi.pinjaman')
            ->layout('layouts.app', ['title' => __('Pinjaman Koperasi')]);
    }
}
