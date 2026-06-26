<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jejak audit untuk fitur "Proses Keluar" anggota: snapshot rekapitulasi keuangan
     * pada saat anggota mengundurkan diri, supaya tetap terlihat di laporan walau
     * koperasi_anggota.status sudah berubah jadi 'keluar'.
     */
    public function up(): void
    {
        Schema::create('koperasi_anggota_keluars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koperasi_anggota_id')->constrained('koperasi_anggota')->cascadeOnDelete();
            $table->decimal('total_simpanan', 15, 2);   // pokok + wajib + sukarela saat keluar
            $table->decimal('sisa_pinjaman', 15, 2);     // total sisa utang berjalan saat keluar
            $table->decimal('dana_dikembalikan', 15, 2); // = total_simpanan - sisa_pinjaman (bisa 0 jika utang lebih besar)
            $table->date('tanggal_keluar');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koperasi_anggota_keluars');
    }
};
