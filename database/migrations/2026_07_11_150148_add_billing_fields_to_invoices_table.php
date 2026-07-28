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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('jenis_tagihan')->nullable()->default('awal'); // awal, uji_lab
            $table->string('file_bukti_bayar')->nullable();
            $table->string('file_kwitansi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_tagihan',
                'file_bukti_bayar',
                'file_kwitansi',
            ]);
        });
    }
};
