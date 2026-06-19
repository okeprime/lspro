<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sub_role')->nullable()->after('role');
        });

        // Migrate legacy roles to sub_roles
        // Client stays client. Superadmin stays superadmin.
        DB::table('users')->whereIn('role', ['tata_usaha', 'pelayanan', 'audit'])->update([
            'sub_role' => DB::raw("CASE 
                                    WHEN role = 'tata_usaha' THEN 'tatausaha' 
                                    WHEN role = 'pelayanan' THEN 'layanan' 
                                    WHEN role = 'audit' THEN 'audit' 
                                   END"),
            'role' => 'admin'
        ]);
        
        // Handle 'tu' just in case
        DB::table('users')->where('role', 'tu')->update(['sub_role' => 'tatausaha', 'role' => 'admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert legacy roles if needed (best effort)
        DB::table('users')->where('role', 'admin')->update([
            'role' => DB::raw("CASE 
                                    WHEN sub_role = 'tatausaha' THEN 'tata_usaha' 
                                    WHEN sub_role = 'layanan' THEN 'pelayanan' 
                                    WHEN sub_role = 'audit' THEN 'audit' 
                                    ELSE 'admin'
                                   END")
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sub_role');
        });
    }
};
