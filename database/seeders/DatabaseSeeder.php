<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ⚠️ PASTIKAN BARIS INI SUDAH DIHAPUS / DIKOMENTAR:
        // \App\Models\User::factory(10)->create();

        // Cukup gunakan ini untuk membuat akun TU (Admin):
        User::updateOrCreate(
            ['email' => 'admin.tu@lspro.go.id'],
            [
                'name' => 'Petugas Tata Usaha',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );
    }
}