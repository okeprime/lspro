<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invoices = \Illuminate\Support\Facades\DB::table('invoices')->whereNotNull('file_bukti_bayar')->get(['id', 'file_bukti_bayar']);
foreach($invoices as $inv) {
    echo $inv->id . ': ' . $inv->file_bukti_bayar . PHP_EOL;
}
