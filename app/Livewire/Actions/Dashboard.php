<?php

namespace App\Livewire\Admin\Koperasi;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.koperasi.dashboard')
            ->layout('layouts.app', ['title' => __('Dashboard Koperasi')]);
    }
}
