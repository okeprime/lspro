<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$p = App\Models\Pengajuan::find(8);
$p->generateAutoInvoice('billing_2');
echo 'DONE';
