<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survailen_schedules', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('pengajuan_id')
                ->constrained('pengajuans')
                ->onDelete('cascade');
            
            $table->year('survailen_year');
            $table->tinyInteger('reminder_month')->unsigned(); // 1-12
            
            $table->boolean('reminder_sent')->default(false);
            $table->dateTime('last_reminder_date')->nullable();
            
            $table->enum('status', ['scheduled', 'in_progress', 'completed'])
                ->default('scheduled');
            
            $table->timestamps();
            
            $table->index('pengajuan_id');
            $table->unique(['pengajuan_id', 'survailen_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survailen_schedules');
    }
};
