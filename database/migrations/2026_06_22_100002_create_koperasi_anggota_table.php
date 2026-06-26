<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel induk anggota koperasi. Entitas terpisah dari tabel nasabah bank sampah.
     */
    public function up(): void
    {
        Schema::create('koperasi_anggota', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_anggota')->unique(); // contoh: KOP-0001
            $table->string('nama');
            $table->string('no_ktp', 20)->unique();
            $table->string('no_telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto')->nullable();

            $table->enum('status', ['aktif', 'pasif', 'keluar'])->default('aktif');
            $table->date('tanggal_bergabung');
            $table->date('tanggal_keluar')->nullable();

            $table->softDeletes(); // jaga histori walau record "dihapus" dari UI
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koperasi_anggota');
    }
};
