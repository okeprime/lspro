<?php

use App\Models\Invoice;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Storage;

echo "Starting file migration...\n";

// 1. Rename Invoice Files (Billing & Kwitansi & Bukti Bayar)
$invoices = Invoice::all();
foreach ($invoices as $invoice) {
    $noPermohonan = str_pad($invoice->pengajuan_id, 5, '0', STR_PAD_LEFT);
    
    // File Invoice (Billing)
    if ($invoice->file_invoice && Storage::disk('public')->exists($invoice->file_invoice)) {
        $ext = pathinfo($invoice->file_invoice, PATHINFO_EXTENSION);
        $newName = 'tagihan_billing_' . $invoice->id . '_permohonan_' . $noPermohonan . '.' . $ext;
        $newPath = 'invoices/' . $newName;
        
        if ($invoice->file_invoice !== $newPath) {
            echo "Renaming billing: {$invoice->file_invoice} -> {$newPath}\n";
            Storage::disk('public')->move($invoice->file_invoice, $newPath);
            $invoice->file_invoice = $newPath;
        }
    }
    
    // File Kwitansi
    if ($invoice->file_kwitansi && Storage::disk('public')->exists($invoice->file_kwitansi)) {
        $ext = pathinfo($invoice->file_kwitansi, PATHINFO_EXTENSION);
        $newName = 'kwitansi_pembayaran_billing_' . $invoice->id . '_permohonan_' . $noPermohonan . '.' . $ext;
        $newPath = 'kwitansi/' . $newName;
        
        if ($invoice->file_kwitansi !== $newPath) {
            echo "Renaming kwitansi: {$invoice->file_kwitansi} -> {$newPath}\n";
            Storage::disk('public')->move($invoice->file_kwitansi, $newPath);
            $invoice->file_kwitansi = $newPath;
        }
    }
    
    // File Bukti Bayar
    if ($invoice->file_bukti_bayar && Storage::disk('public')->exists($invoice->file_bukti_bayar)) {
        $ext = pathinfo($invoice->file_bukti_bayar, PATHINFO_EXTENSION);
        $newName = 'bukti_bayar_billing_' . $invoice->id . '_permohonan_' . $noPermohonan . '.' . $ext;
        $newPath = 'bukti_pembayaran/' . $newName;
        
        if ($invoice->file_bukti_bayar !== $newPath) {
            echo "Renaming bukti bayar: {$invoice->file_bukti_bayar} -> {$newPath}\n";
            Storage::disk('public')->move($invoice->file_bukti_bayar, $newPath);
            $invoice->file_bukti_bayar = $newPath;
        }
    }
    
    $invoice->save();
}

// 2. Rename Pengajuan Files
$pengajuans = Pengajuan::all();
foreach ($pengajuans as $pengajuan) {
    $noPermohonan = str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT);
    $dataForm = is_array($pengajuan->data_form) ? $pengajuan->data_form : (json_decode($pengajuan->data_form, true) ?? []);
    $changed = false;
    
    // Sertifikat
    if ($pengajuan->file_sertifikat && Storage::disk('public')->exists($pengajuan->file_sertifikat)) {
        $ext = pathinfo($pengajuan->file_sertifikat, PATHINFO_EXTENSION);
        $newName = 'sertifikat_lspro_permohonan_' . $noPermohonan . '.' . $ext;
        $newPath = 'sertifikat/' . $newName;
        
        if ($pengajuan->file_sertifikat !== $newPath) {
            echo "Renaming sertifikat: {$pengajuan->file_sertifikat} -> {$newPath}\n";
            Storage::disk('public')->move($pengajuan->file_sertifikat, $newPath);
            $pengajuan->file_sertifikat = $newPath;
            $changed = true;
        }
    }
    
    // Jadwal Audit (bisa di $pengajuan->data_form['dokumen_jadwal'])
    if (isset($dataForm['dokumen_jadwal']) && Storage::disk('public')->exists($dataForm['dokumen_jadwal'])) {
        $ext = pathinfo($dataForm['dokumen_jadwal'], PATHINFO_EXTENSION);
        $newName = 'jadwal_audit_permohonan_' . $noPermohonan . '.' . $ext;
        $newPath = 'jadwal/' . $newName;
        
        if ($dataForm['dokumen_jadwal'] !== $newPath) {
            echo "Renaming jadwal: {$dataForm['dokumen_jadwal']} -> {$newPath}\n";
            Storage::disk('public')->move($dataForm['dokumen_jadwal'], $newPath);
            $dataForm['dokumen_jadwal'] = $newPath;
            $changed = true;
        }
    }
    
    // Surat Permohonan TTD
    if ($pengajuan->file_permohonan_ttd && Storage::disk('public')->exists($pengajuan->file_permohonan_ttd)) {
        $ext = pathinfo($pengajuan->file_permohonan_ttd, PATHINFO_EXTENSION);
        $newName = 'surat_permohonan_ttd_permohonan_' . $noPermohonan . '.' . $ext;
        $folderPengajuan = 'permohonan/' . $pengajuan->id;
        $newPath = $folderPengajuan . '/' . $newName;
        
        if ($pengajuan->file_permohonan_ttd !== $newPath) {
            echo "Renaming permohonan ttd: {$pengajuan->file_permohonan_ttd} -> {$newPath}\n";
            Storage::disk('public')->move($pengajuan->file_permohonan_ttd, $newPath);
            $pengajuan->file_permohonan_ttd = $newPath;
            $changed = true;
        }
    }
    
    // LKS (Tindakan Perbaikan)
    if (isset($dataForm['file_tindakan_perbaikan']) && Storage::disk('public')->exists('tindakan_perbaikan/' . $dataForm['file_tindakan_perbaikan'])) {
        $oldPathRelative = $dataForm['file_tindakan_perbaikan']; 
        $oldPathFull = 'tindakan_perbaikan/' . $oldPathRelative;
        $ext = pathinfo($oldPathFull, PATHINFO_EXTENSION);
        $newName = 'lks_tindakan_perbaikan_permohonan_' . $noPermohonan . '.' . $ext;
        $newPathFull = 'tindakan_perbaikan/' . $pengajuan->id . '/' . $newName;
        
        if ($oldPathFull !== $newPathFull) {
            echo "Renaming LKS: {$oldPathFull} -> {$newPathFull}\n";
            Storage::disk('public')->move($oldPathFull, $newPathFull);
            $dataForm['file_tindakan_perbaikan'] = $pengajuan->id . '/' . $newName;
            $changed = true;
        }
    }

    if ($changed) {
        $pengajuan->data_form = $dataForm;
        $pengajuan->save();
    }
}

echo "Migration finished!\n";
