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
            $table->dateTime('jadwal_audit')->nullable();
            $table->dateTime('tanggal_pengambilan_contoh')->nullable();
            $table->boolean('is_jadwal_disetujui')->nullable()->default(null);
            $table->string('file_lhp')->nullable();
            $table->dateTime('batas_waktu_lhp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->dropColumn([
                'jadwal_audit',
                'tanggal_pengambilan_contoh',
                'is_jadwal_disetujui',
                'file_lhp',
                'batas_waktu_lhp',
            ]);
        });
    }
};
