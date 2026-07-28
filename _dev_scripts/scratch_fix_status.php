<?php
$p = \App\Models\Pengajuan::find(5);
if($p && $p->status === 'draft') {
    $p->status = 'menunggu_ttd';
    $p->save();
    echo "Updated status to menunggu_ttd\n";
} else {
    echo "Not a draft or not found\n";
}
