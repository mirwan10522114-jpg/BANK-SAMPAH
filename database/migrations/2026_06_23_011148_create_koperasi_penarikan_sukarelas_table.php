<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('koperasi_penarikan_sukarelas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan')->unique();
            $table->foreignId('koperasi_anggota_id')->constrained('koperasi_anggota')->cascadeOnDelete();
            $table->decimal('jumlah', 15, 2);
            $table->text('alasan');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'dicairkan'])->default('menunggu');
            
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->string('nama_pengurus')->nullable(); 
            $table->timestamp('tanggal_pencairan')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koperasi_penarikan_sukarelas');
    }
};