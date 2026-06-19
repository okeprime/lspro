<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_documents', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('audit_finding_id')
                ->constrained('audit_findings')
                ->onDelete('cascade');
            
            $table->string('document_name');
            $table->string('file_path');
            
            $table->dateTime('upload_date');
            
            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->timestamps();
            
            $table->index('audit_finding_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_documents');
    }
};
