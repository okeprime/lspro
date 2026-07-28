<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = App\Models\Pengajuan::find(20);
if ($p) {
    $p->status = 'proses_evaluasi';
    $p->save();

    $h = $p->statusHistories()->latest()->first();
    if($h){ 
        $h->to_status = 'proses_evaluasi';
        $h->notes = 'Dokumen Perjanjian telah ditandatangani. Menunggu Tim Audit memeriksa dan mengatur Jadwal Audit.';
        $h->save(); 
    }
    echo "OK";
}
