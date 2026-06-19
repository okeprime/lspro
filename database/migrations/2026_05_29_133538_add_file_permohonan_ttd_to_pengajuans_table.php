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

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('tahap');

            $table->string('nomor_registrasi')->nullable();

            $table->string('jenis_pengajuan')
                ->default('Sertifikasi');

            $table->string('status')
                ->default('menunggu_ttd');

            $table->text('keterangan_admin')->nullable();

            $table->json('data_form')->nullable();

            // Draft hasil generate sistem
            $table->string('file_permohonan')->nullable();

            // PDF hasil scan tanda tangan + materai client
            $table->string('file_permohonan_ttd')->nullable();

            $table->text('ceklis_dokumen')->nullable();

            $table->text('catatan')->nullable();

            $table->string('nama_tu')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};