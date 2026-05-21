<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            // INI DIA KOLOM YANG HILANG TADI:
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            
            $table->string('tahap');
            $table->string('nomor_registrasi')->nullable();
            $table->string('jenis_pengajuan')->default('Sertifikasi');
            $table->string('status')->default('Diajukan');
            $table->text('keterangan_admin')->nullable();
            $table->json('data_form')->nullable();
            $table->string('file_permohonan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};