<?php

namespace App\Livewire\Admin\Koperasi;

use Livewire\Component;

class Simpanan extends Component
{
    public function render()
    {
        return view('livewire.admin.koperasi.simpanan')
            ->layout('layouts.app', ['title' => __('Simpanan Koperasi')]);
    }
}
