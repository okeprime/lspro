import sys

file_path = 'app/Http/Controllers/PengajuanController.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

old_upload = '''    public function uploadPermohonanTtd(Request $request, $id)
    {
        $pengajuan = Pengajuan::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'file_permohonan_ttd' => 'required|file|mimes:pdf|max:10240',
            'file_ceklis_ttd' => 'required|file|mimes:pdf|max:10240',
        ]);

        $folderPengajuan = 'permohonan/' . $pengajuan->id;
        Storage::disk('public')->makeDirectory($folderPengajuan);

        // Permohonan 7.2-1
        if ($pengajuan->file_permohonan_ttd && Storage::disk('public')->exists($pengajuan->file_permohonan_ttd)) {
            Storage::disk('public')->delete($pengajuan->file_permohonan_ttd);
        }
        Storage::disk('public')->putFileAs(
            $folderPengajuan,
            $request->file('file_permohonan_ttd'),
            'permohonan_ttd.pdf'
        );
        $pengajuan->file_permohonan_ttd = $folderPengajuan . '/permohonan_ttd.pdf';

        if (in_array($pengajuan->status, ['perjanjian', 'menunggu_ttd'], true)) {
            $pengajuan->transitionTo(
                'billing',
                'Dokumen permohonan bermeterai dan lampiran telah diunggah. Menunggu penerbitan tagihan (Billing).',
                Auth::id()
            );
            
            // Trigger Notification
            NotificationHelper::sendToRole('tatausaha', 'Berkas Dilampirkan', 'Client telah mengunggah dokumen TTD dan lampiran untuk pengajuan #' . $pengajuan->id, 'info', $pengajuan->id);
            NotificationHelper::sendToUser(Auth::id(), 'Berkas Terkirim', 'Dokumen permohonan Anda telah berhasil dikirim. Menunggu proses billing.', 'success', $pengajuan->id);
        } else {
            $pengajuan->save();
        }

        return redirect()
            ->route('aktivitas.index')
            ->with('success', 'Dokumen Permohonan Bertanda Tangan berhasil diunggah! Berkas Anda akan segera diproses.');
    }'''

new_upload = '''    public function uploadPermohonanTtd(Request $request, $id)
    {
        $pengajuan = Pengajuan::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'file_permohonan_ttd' => 'required|file|mimes:pdf|max:10240',
            'file_ceklis_ttd' => 'required|file|mimes:pdf|max:10240',
        ]);

        $folderPengajuan = 'permohonan/' . $pengajuan->id;
        Storage::disk('public')->makeDirectory($folderPengajuan);

        // Permohonan 7.2-1
        if ($pengajuan->file_permohonan_ttd && Storage::disk('public')->exists($pengajuan->file_permohonan_ttd)) {
            Storage::disk('public')->delete($pengajuan->file_permohonan_ttd);
        }
        Storage::disk('public')->putFileAs(
            $folderPengajuan,
            $request->file('file_permohonan_ttd'),
            'permohonan_ttd.pdf'
        );
        $pengajuan->file_permohonan_ttd = $folderPengajuan . '/permohonan_ttd.pdf';

        // Ceklis 7.2-4
        if ($pengajuan->file_ceklis_ttd && Storage::disk('public')->exists($pengajuan->file_ceklis_ttd)) {
            Storage::disk('public')->delete($pengajuan->file_ceklis_ttd);
        }
        Storage::disk('public')->putFileAs(
            $folderPengajuan,
            $request->file('file_ceklis_ttd'),
            'ceklis_ttd.pdf'
        );
        $pengajuan->file_ceklis_ttd = $folderPengajuan . '/ceklis_ttd.pdf';

        $pengajuan->transitionTo(
            'billing',
            'Dokumen TTD klien telah diterima. Lanjut ke proses tagihan sertifikasi (Billing).',
            Auth::id()
        );
        $pengajuan->save();

        NotificationHelper::sendToRole('tatausaha', 'Dokumen TTD Diunggah', 'Klien telah mengunggah Form 7.2-1 dan Form 7.2-4 bertanda tangan untuk pengajuan #' . $pengajuan->id, 'success', $pengajuan->id);
        NotificationHelper::sendToUser(Auth::id(), 'Berkas Terkirim', 'Dokumen permohonan Anda telah berhasil dikirim. Menunggu proses billing.', 'success', $pengajuan->id);

        return redirect()
            ->route('aktivitas.index')
            ->with('success', 'Dokumen Bertanda Tangan berhasil diunggah! Berkas Anda akan segera masuk tahap Pembayaran (Billing).');
    }'''

content = content.replace(old_upload, new_upload)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Done")
