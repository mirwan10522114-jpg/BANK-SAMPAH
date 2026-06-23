<?php

namespace App\Livewire\Actions;

use Livewire\Component;

class Pengaturan extends Component
{
    public function render()
    {
        return view('pages.admin.koperasi.pengaturan')
            ->layout('layouts.app', ['title' => __('Pengaturan Koperasi')]);
    }
}