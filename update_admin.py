import sys

file_path = 'app/Http/Controllers/AdminController.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update validation
old_val = "'kesimpulan' => 'required|in:perjanjian,perbaikan'"
new_val = "'kesimpulan' => 'required|in:evaluasi_724_audit,menunggu_ttd,perbaikan'"
content = content.replace(old_val, new_val)

# 2. Update transition logic
old_transition = '''        } elseif ($statusNormalized === 'evaluasi_724_audit') {
            // Audit Submit -> Generate form dan Lanjut ke Menunggu TTD
            $pengajuan->refresh();
            $form723Generated = $this->generateForm723($pengajuan);
            
            $pengajuan->transitionTo(
                'menunggu_ttd',
                $request->catatan ?: 'Evaluasi Form 7.2-4 selesai. Klien dapat mengunduh dan menandatangani berkas.',
                auth()->id()
            );

            NotificationHelper::sendToUser($pengajuan->user_id, 'Evaluasi Selesai', 'Evaluasi dokumen Form 7.2-4 selesai. Silakan unduh dan unggah dokumen TTD.', 'success', $pengajuan->id);

            return redirect()->route('admin.audit_724')->with(
                'success',
                $form723Generated 
                    ? 'Audit kebenaran selesai. Form 7.2-1 & 7.2-4 siap diunduh klien.'
                    : 'Audit kebenaran selesai. (Template Form 7.2-3 belum ada).'
            );
        }'''

new_transition = '''        } elseif ($statusNormalized === 'evaluasi_724_audit') {
            // Audit Submit -> Lanjut ke Menunggu TTD (Klien download Form 7.2-1 dan 7.2-4)
            $pengajuan->refresh();
            
            $pengajuan->transitionTo(
                'menunggu_ttd',
                $request->catatan ?: 'Evaluasi Form 7.2-4 selesai. Klien dapat mengunduh dan menandatangani berkas Form 7.2-1 dan Form 7.2-4.',
                auth()->id()
            );

            \\App\\Helpers\\NotificationHelper::sendToUser($pengajuan->user_id, 'Evaluasi Selesai', 'Evaluasi dokumen Form 7.2-4 selesai. Silakan unduh dan unggah dokumen TTD Form 7.2-1 dan 7.2-4.', 'success', $pengajuan->id);

            return redirect()->route('admin.dashboard')->with('success', 'Audit kebenaran Form 7.2-4 selesai. Form siap diunduh klien.');
        }'''
content = content.replace(old_transition, new_transition)

# 3. Append terimaAwal
new_method = '''
    public function terimaAwal(Request $request, $id)
    {
        $request->validate([
            'kesimpulan' => 'required|in:menunggu_lampiran,perbaikan',
            'catatan' => 'nullable|string'
        ]);

        $pengajuan = Pengajuan::findOrFail($id);
        
        $pengajuan->transitionTo(
            $request->kesimpulan,
            $request->catatan ?? 'Pengecekan awal telah dilakukan.',
            auth()->id()
        );

        $pengajuan->catatan_admin = $request->catatan;
        $pengajuan->save();

        if ($request->kesimpulan === 'perbaikan') {
            \\App\\Helpers\\NotificationHelper::sendToUser($pengajuan->user_id, 'Pengecekan Awal: Revisi Diperlukan', 'Pengajuan Anda ditolak/direvisi dengan catatan: ' . $request->catatan, 'warning', $pengajuan->id);
        } else {
            \\App\\Helpers\\NotificationHelper::sendToUser($pengajuan->user_id, 'Pengecekan Awal Selesai', 'Pengecekan awal selesai. Silakan unggah dokumen kelengkapan.', 'success', $pengajuan->id);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Pengecekan awal berhasil disimpan.');
    }
}'''
content = content.replace('\n}\n', new_method)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Done")
