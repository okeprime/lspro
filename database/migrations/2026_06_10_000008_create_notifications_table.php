<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->foreignId('pengajuan_id')
                ->nullable()
                ->constrained('pengajuans')
                ->onDelete('cascade');
            
            $table->enum('type', [
                'reminder_survailen',
                'dokumen_ditolak',
                'audit_scheduled',
                'pembayaran_invoice',
                'perubahan_status'
            ]);
            
            $table->string('title');
            $table->text('message');
            
            $table->boolean('is_read')->default(false);
            
            $table->boolean('email_sent')->default(false);
            $table->dateTime('email_sent_at')->nullable();
            
            $table->dateTime('read_at')->nullable();
            
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('pengajuan_id');
            $table->index('is_read');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
