<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_findings', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('pengajuan_id')
                ->constrained('pengajuans')
                ->onDelete('cascade');
            
            $table->enum('audit_type', ['kecukupan', 'kesesuaian']);
            $table->string('finding_number'); // e.g., "01", "02", etc
            $table->text('description');
            
            // Hanya untuk audit_kesesuaian
            $table->enum('severity', ['minor', 'mayor', 'observation'])
                ->nullable();
            
            $table->text('action_required');
            
            $table->enum('status', ['open', 'in_progress', 'closed', 'requires_verification'])
                ->default('open');
            
            // PIC yang bertanggung jawab
            $table->foreignId('pic_responsible_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            
            $table->timestamps();
            
            $table->index('pengajuan_id');
            $table->index('audit_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_findings');
    }
};
