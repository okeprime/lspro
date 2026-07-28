<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$inv = App\Models\Invoice::find(10);
$inv->file_invoice = 'invoices/vTPFZjnEFm2pH5ibpb3APEeLJVEGQmhbQIIFeopt.png';
$inv->save();
echo 'DONE';
