<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tanpa bunga: angsuran_per_bulan = jumlah_pinjaman / tenor_bulan.
     * biaya_admin tetap disediakan sebagai potongan satu-kali (opsional, default 0) saat pencairan,
     * bukan bunga berjalan.
     */
    public function up(): void
    {
        Schema::create('koperasi_pinjamans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pinjaman')->unique(); // contoh: PINJ-20260622-0001
            $table->foreignId('koperasi_anggota_id')->constrained('koperasi_anggota')->cascadeOnDelete();

            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->unsignedInteger('tenor_bulan');
            $table->decimal('angsuran_per_bulan', 15, 2);
            $table->decimal('biaya_admin', 15, 2)->default(0);

            $table->date('tanggal_pengajuan');
            $table->date('tanggal_pencairan')->nullable();
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak', 'berjalan', 'lunas'])->default('diajukan');
            $table->decimal('sisa_pinjaman', 15, 2); // di-set = jumlah_pinjaman saat disetujui, berkurang tiap angsuran

            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // pengurus yang approve

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koperasi_pinjamans');
    }
};
