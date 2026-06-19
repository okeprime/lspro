<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('123456');

        $users = [
            [
                'nama_perusahaan' => 'LSPro BRMP SDLP',
                'nama_penghubung' => 'Ketua LSPro',
                'email' => 'superadmin@lspro.local',
                'password' => $password,
                'role' => 'superadmin',
                'sub_role' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_perusahaan' => 'Divisi Tata Usaha',
                'nama_penghubung' => 'Admin Tata Usaha',
                'email' => 'tu@lspro.local',
                'password' => $password,
                'role' => 'admin',
                'sub_role' => 'tatausaha',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_perusahaan' => 'Divisi Layanan',
                'nama_penghubung' => 'Admin Layanan',
                'email' => 'layanan@lspro.local',
                'password' => $password,
                'role' => 'admin',
                'sub_role' => 'layanan',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_perusahaan' => 'Divisi Audit',
                'nama_penghubung' => 'Admin Audit',
                'email' => 'audit@lspro.local',
                'password' => $password,
                'role' => 'admin',
                'sub_role' => 'audit',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_perusahaan' => 'PT Client Testing',
                'nama_penghubung' => 'Client Testing',
                'email' => 'client@lspro.local',
                'password' => $password,
                'role' => 'client',
                'sub_role' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
