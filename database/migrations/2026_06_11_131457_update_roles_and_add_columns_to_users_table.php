<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });

        // Migrasi Role Lama: admin -> tata_usaha
        DB::table('users')->where('role', 'admin')->update(['role' => 'tata_usaha']);

        // Buat Superadmin Pertama
        DB::table('users')->updateOrInsert(
            ['email' => 'superadmin@lspro.id'],
            [
                'nama_perusahaan' => 'LSPro BRMP SDLP',
                'nama_penghubung' => 'Ketua LSPro',
                'no_telp' => '-',
                'alamat' => '-',
                'password' => Hash::make('password'), // default password
                'role' => 'superadmin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
        
        DB::table('users')->where('role', 'tata_usaha')->update(['role' => 'admin']);
        DB::table('users')->where('email', 'superadmin@lspro.id')->delete();
    }
};
