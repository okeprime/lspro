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
            $table->boolean('is_draft')->default(false)->after('status');
            $table->integer('current_step')->default(1)->after('is_draft');
            $table->integer('progress_percent')->default(0)->after('current_step');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->dropColumn(['is_draft', 'current_step', 'progress_percent']);
        });
    }
};
