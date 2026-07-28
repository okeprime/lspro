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
        Schema::create('rab_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            
            $table->string('kategori'); // e.g. 'Permohonan', 'Audit Kesesuaian'
            $table->string('komponen')->nullable(); // e.g. 'Auditor Kepala', 'Narsum'
            
            $table->integer('hari')->nullable();
            $table->integer('orang')->nullable();
            
            $table->decimal('tarif_pnbp_satuan', 12, 2)->nullable(); // Tarif PNBP/Orang/Hari
            $table->decimal('tarif_pnbp_total', 12, 2)->nullable(); // Tarif PNBP total
            $table->decimal('izin_penggunaan', 12, 2)->nullable(); // Izin Penggunaan Keterangan
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab_items');
    }
};
