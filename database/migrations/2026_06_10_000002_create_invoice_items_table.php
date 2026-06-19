<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->onDelete('cascade');
            
            $table->enum('tahap', [
                'sertifikasi_tahap_1',
                'sertifikasi_tahap_2',
                'audit_kecukupan',
                'audit_kesesuaian',
                'penerbitan'
            ]);
            
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->integer('quantity')->default(1);
            
            $table->timestamps();
            
            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
