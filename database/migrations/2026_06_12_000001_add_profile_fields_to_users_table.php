<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Field untuk Admin / Internal
            $table->string('nip')->nullable()->after('sub_role');
            $table->string('unit_kerja')->nullable()->after('nip');
            $table->string('jabatan')->nullable()->after('unit_kerja');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'unit_kerja', 'jabatan']);
        });
    }
};
