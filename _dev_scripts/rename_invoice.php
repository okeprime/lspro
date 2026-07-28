<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$inv = App\Models\Invoice::find(10);
$oldFile = 'storage/app/public/' . $inv->file_invoice;
$newFileName = 'invoices/tagihan_' . $inv->jenis_tagihan . '_' . $inv->pengajuan_id . '_' . time() . '.png';
$newFile = 'storage/app/public/' . $newFileName;
rename(base_path($oldFile), base_path($newFile));
$inv->file_invoice = $newFileName;
$inv->save();
echo 'DONE';
