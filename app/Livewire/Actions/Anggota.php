<?php

namespace App\Livewire\Admin\Koperasi;

use Livewire\Component;

class Anggota extends Component
{
    public function render()
    {
        return view('livewire.admin.koperasi.anggota')
            ->layout('layouts.app', ['title' => __('Anggota Koperasi')]);
    }
}
