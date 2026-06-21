<?php

namespace App\Livewire\Admin\Koperasi;

use Livewire\Component;

class Pengaturan extends Component
{
    public function render()
    {
        return view('livewire.admin.koperasi.pengaturan')
            ->layout('layouts.app', ['title' => __('Pengaturan Koperasi')]);
    }
}
