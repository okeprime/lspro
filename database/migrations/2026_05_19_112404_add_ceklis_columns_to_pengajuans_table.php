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
        Schema::table('pengajuans', function (Blueprint $table) {
            // Menambahkan kolom ceklis, catatan, dan nama_tu (dibuat nullable agar aman)
            $table->text('ceklis_dokumen')->nullable()->after('status');
            $table->text('catatan')->nullable()->after('ceklis_dokumen');
            $table->string('nama_tu')->nullable()->after('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            // Menghapus kembali kolom jika migrasi di-rollback
            $table->dropColumn(['ceklis_dokumen', 'catatan', 'nama_tu']);
        });
    }
};