<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survailen_schedules', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->after('pengajuan_id');
            $table->date('deadline')->nullable()->after('reminder_sent');
            $table->text('catatan')->nullable()->after('deadline');
            $table->enum('status_dokumen', ['menunggu', 'diterima', 'selesai'])->default('menunggu')->after('catatan');
            $table->string('file_dokumen', 255)->nullable()->after('status_dokumen');
        });
    }

    public function down(): void
    {
        Schema::table('survailen_schedules', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'deadline', 'catatan', 'status_dokumen', 'file_dokumen']);
        });
    }
};
