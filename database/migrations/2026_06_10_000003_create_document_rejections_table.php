<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_rejections', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('pengajuan_id')
                ->constrained('pengajuans')
                ->onDelete('cascade');
            
            $table->string('document_category');
            $table->string('document_name');
            $table->text('rejection_reason')->nullable();
            $table->text('catatan_kurang')->nullable(); // apa yang kurang
            $table->text('catatan_tu')->nullable(); // catatan TU -> visible to klien
            
            $table->dateTime('rejected_at')->nullable();
            $table->dateTime('resubmitted_at')->nullable();
            
            $table->enum('status', ['pending', 'rejected', 'resubmitted', 'approved'])
                ->default('pending');
            
            $table->timestamps();
            
            $table->index('pengajuan_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_rejections');
    }
};
