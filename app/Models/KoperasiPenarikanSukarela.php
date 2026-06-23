<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KoperasiPenarikanSukarela extends Model
{
    use HasFactory;

    // Mengizinkan semua kolom diisi secara massal (Mass Assignment)
    protected $guarded = [];

    // Relasi balik ke tabel anggota
    public function anggota()
    {
        return $this->belongsTo(KoperasiAnggota::class, 'koperasi_anggota_id');
    }
}