<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pengaturan koperasi (singleton - biasanya hanya berisi 1 baris).
     */
    public function up(): void
    {
        Schema::create('koperasi_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_koperasi');
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->string('logo')->nullable();

            // Parameter keuangan standar
            $table->decimal('nominal_simpanan_pokok', 15, 2)->default(0);
            $table->decimal('nominal_simpanan_wajib', 15, 2)->default(0);
            $table->decimal('biaya_admin_pinjaman', 15, 2)->default(0); // biaya admin flat per pengajuan pinjaman (bukan bunga)

            // Saldo kas awal, dipakai sebagai titik mulai perhitungan Total Kas & Neraca
            $table->decimal('saldo_kas_awal', 15, 2)->default(0);
            $table->date('tanggal_saldo_awal')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koperasi_settings');
    }
};
