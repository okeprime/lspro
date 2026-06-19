$p = App\Models\Pengajuan::find(2);
if ($p) {
    $p->status = 'billing';
    $p->save();
    $p->invoices()->create([
        'invoice_number' => 'INV-LSPRO-2026-00001',
        'invoice_date' => now(),
        'due_date' => now()->addDays(7),
        'amount_total' => 15000000,
        'status' => 'unpaid'
    ]);
    echo 'Invoice created';
}
