DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('unpaid', 'pending_verification', 'paid', 'overdue', 'cancelled') NOT NULL DEFAULT 'unpaid'");
echo 'Done';
