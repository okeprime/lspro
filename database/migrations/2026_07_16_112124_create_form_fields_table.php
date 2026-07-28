<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->string('section')->nullable()->comment('Bagian form (misal: Identitas Pemohon, Legalitas)');
            $table->string('label')->comment('Pertanyaan / Label yang tampil di layar');
            $table->string('name')->unique()->comment('Nama variabel (contoh: nama_pemohon, alamat)');
            $table->enum('type', ['text', 'textarea', 'number', 'email', 'date', 'select', 'radio', 'file'])->default('text');
            $table->json('options')->nullable()->comment('Pilihan untuk select/radio');
            $table->boolean('is_required')->default(true);
            $table->integer('order_index')->default(0)->comment('Urutan tampil');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
