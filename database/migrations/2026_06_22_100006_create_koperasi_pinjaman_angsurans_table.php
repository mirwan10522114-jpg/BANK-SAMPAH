<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('koperasi_pinjaman_angsurans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koperasi_pinjaman_id')->constrained('koperasi_pinjamans')->cascadeOnDelete();
            $table->unsignedInteger('angsuran_ke'); // urutan termin, dideteksi otomatis (MAX(angsuran_ke)+1)
            $table->decimal('jumlah_bayar', 15, 2);
            $table->date('tanggal_bayar');
            $table->decimal('sisa_pinjaman_setelah', 15, 2); // snapshot sisa utang setelah bayar ini
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['koperasi_pinjaman_id', 'angsuran_ke'], 'pinjaman_angsurans_ke_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koperasi_pinjaman_angsurans');
    }
};