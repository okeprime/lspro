<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pengajuan;
use App\Models\StatusHistory;
use App\Support\LsproType5Workflow;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('email', 'client@lspro.local')->first();
        if (!$user) {
            echo "Tester user not found.\n";
            return;
        }

        // Buat satu pengajuan
        $pengajuan = Pengajuan::firstOrCreate(
            [
                'user_id' => $user->id,
                'jenis_pengajuan' => 'sertifikasi',
            ],
            [
                'nomor_pengajuan' => 'REG-1001-DEMO',
                'status' => 'keputusan',
                'data_form' => ['nama_produk' => 'Beras Organik Sejahtera'],
                'tanggal_pengajuan' => Carbon::now()->subDays(15),
            ]
        );

        // Pastikan status terakhirnya kepututsan
        $pengajuan->update(['status' => 'keputusan']);

        // Seed timeline stages
        $stages = LsproType5Workflow::STAGES;
        $i = 15;
        foreach ($stages as $key => $stage) {
            StatusHistory::updateOrCreate(
                [
                    'pengajuan_id' => $pengajuan->id,
                    'status' => $key
                ],
                [
                    'actor_id' => 1,
                    'notes' => 'Telah diselesaikan oleh sistem (Demo).',
                    'created_at' => Carbon::now()->subDays($i)
                ]
            );
            $i--;
        }

        echo "Success! Demo activity tracking populated for client@lspro.local\n";
    }
}
