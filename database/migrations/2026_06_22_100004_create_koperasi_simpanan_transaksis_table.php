<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat (ledger) setiap setoran/penarikan simpanan. Append-only, jangan diupdate/dihapus
     * agar Mutasi Kas & Laporan tetap akurat.
     */
    public function up(): void
    {
        Schema::create('koperasi_simpanan_transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique(); // contoh: SIM-20260622-0001
            $table->foreignId('koperasi_anggota_id')->constrained('koperasi_anggota')->cascadeOnDelete();
            $table->enum('jenis_simpanan', ['pokok', 'wajib', 'sukarela']);
            $table->enum('tipe', ['setor', 'tarik']);
            $table->decimal('jumlah', 15, 2);
            $table->decimal('saldo_sebelum', 15, 2);
            $table->decimal('saldo_sesudah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->dateTime('tanggal_transaksi');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // pengurus yang input
            $table->timestamps();

            $table->index(['koperasi_anggota_id', 'jenis_simpanan'], 'simpanan_transaksis_anggota_jenis_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koperasi_simpanan_transaksis');
    }
};