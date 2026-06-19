<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuans')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['pengajuan_id', 'created_at']);
        });

        DB::table('pengajuans')
            ->select(['id', 'user_id', 'status', 'created_at', 'updated_at'])
            ->orderBy('id')
            ->chunkById(100, function ($pengajuans) {
                $rows = $pengajuans->map(fn ($pengajuan) => [
                    'pengajuan_id' => $pengajuan->id,
                    'actor_id' => $pengajuan->user_id,
                    'from_status' => null,
                    'to_status' => $pengajuan->status,
                    'notes' => 'Status awal saat riwayat Alur Sertifikasi Tipe 5 diaktifkan.',
                    'created_at' => $pengajuan->updated_at ?? $pengajuan->created_at,
                    'updated_at' => $pengajuan->updated_at ?? $pengajuan->created_at,
                ])->all();

                DB::table('pengajuan_status_histories')->insert($rows);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_status_histories');
    }
};
