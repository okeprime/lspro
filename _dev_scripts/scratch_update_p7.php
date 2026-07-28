<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = \App\Models\Pengajuan::find(7);
if ($p) {
    $p->status = 'menunggu_lhp';
    $p->save();
    echo "Status updated to menunggu_lhp\n";
} else {
    echo "Pengajuan 7 not found\n";
}
