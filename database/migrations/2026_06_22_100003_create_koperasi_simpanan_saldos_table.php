<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Saldo TERKINI per anggota per jenis simpanan (pokok/wajib/sukarela).
     * Tabel ini adalah "cache" agar saldo cepat ditampilkan di form transaksi & dashboard,
     * sumber kebenarannya tetap riwayat di koperasi_simpanan_transaksis.
     */
    public function up(): void
    {
        Schema::create('koperasi_simpanan_saldos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koperasi_anggota_id')->constrained('koperasi_anggota')->cascadeOnDelete();
            $table->enum('jenis_simpanan', ['pokok', 'wajib', 'sukarela']);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['koperasi_anggota_id', 'jenis_simpanan'], 'simpanan_saldos_anggota_jenis_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koperasi_simpanan_saldos');
    }
};