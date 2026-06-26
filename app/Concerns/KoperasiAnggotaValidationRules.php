<?php

namespace App\Concerns;

trait KoperasiAnggotaValidationRules
{
    protected function koperasiAnggotaRules(?int $editingId = null): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'no_ktp' => ['required', 'string', 'max:20', 'unique:koperasi_anggota,no_ktp,'.$editingId],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'tanggal_bergabung' => ['required', 'date'],
            'status' => ['nullable', 'in:aktif,pasif'],
        ];
    }
}