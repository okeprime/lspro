<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bandings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pengajuan_id')->nullable()->constrained('pengajuans')->onDelete('set null');
            $table->enum('jenis', ['banding', 'keluhan', 'laporan']);
            $table->text('pesan');
            $table->string('file_lampiran', 255)->nullable();
            $table->enum('status', ['terkirim', 'diproses', 'selesai'])->default('terkirim');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bandings');
    }
};
