<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resertifikasi_scope_changes', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('pengajuan_id')
                ->constrained('pengajuans')
                ->onDelete('cascade');
            
            $table->enum('tipe_perubahan', ['penambahan', 'pengurangan']);
            
            $table->text('scope_description_old');
            $table->text('scope_description_new');
            $table->text('alasan_perubahan');
            
            $table->string('surat_perubahan_file')->nullable(); // upload surat
            $table->string('dokumen_pendukung_file')->nullable(); // dokumen supporting
            
            $table->enum('status', ['submitted', 'approved', 'rejected'])
                ->default('submitted');
            
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            
            $table->index('pengajuan_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resertifikasi_scope_changes');
    }
};
