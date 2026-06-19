<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@lspro.local'],
            [
                'nama_penghubung' => 'Ketua LSPro',
                'nama_perusahaan' => 'Admin System',
                'no_telp' => '-',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );
        
        // Ensure the role is superadmin just in case it was created earlier with a different role
        $admin->update([
            'role' => 'superadmin',
            'is_active' => true,
        ]);
        
        $this->command->info('Superadmin created with email: admin@lspro.local and password: password123');
    }
}
