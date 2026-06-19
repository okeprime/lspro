<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('pengajuan_id')
                ->constrained('pengajuans')
                ->onDelete('cascade');
            
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            
            $table->decimal('amount_total', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            
            $table->dateTime('payment_date')->nullable();
            $table->enum('payment_method', ['bank_transfer', 'cash', 'other'])->nullable();
            
            $table->text('notes')->nullable();
            $table->enum('status', ['unpaid', 'paid', 'overdue', 'cancelled'])->default('unpaid');
            
            $table->timestamps();
            
            $table->index('pengajuan_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
