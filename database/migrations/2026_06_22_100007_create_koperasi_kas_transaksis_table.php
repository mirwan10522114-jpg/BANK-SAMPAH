<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ledger TUNGGAL untuk semua arus kas koperasi (Total Kas dashboard & Mutasi Kas report
     * dihitung dari sini). Setiap setor/tarik simpanan, pencairan pinjaman, pembayaran angsuran,
     * dan dana keluar anggota WAJIB menambahkan 1 baris di sini via observer/service,
     * supaya saldo kas selalu konsisten dengan transaksi aslinya.
     */
    public function up(): void
    {
        Schema::create('koperasi_kas_transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_referensi')->nullable(); // mengacu ke nomor_transaksi simpanan / nomor_pinjaman, dst.
            $table->enum('sumber', ['simpanan', 'pinjaman', 'angsuran', 'anggota_keluar', 'lainnya']);
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->dateTime('tanggal_transaksi');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tanggal_transaksi', 'tipe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koperasi_kas_transaksis');
    }
};
