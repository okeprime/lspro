<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SuperadminController extends Controller
{
    /**
     * Menampilkan Dasbor Pengaturan Superadmin
     */
    public function settings()
    {
        // 1. Baca Kebijakan dari JSON (jika ada)
        $settingsPath = storage_path('app/settings.json');
        
        $settings = [
            'kebijakan_umum' => 'Kebijakan dasar sistem sertifikasi LSPro BRMP SDLP.',
        ];

        if (File::exists($settingsPath)) {
            $json = File::get($settingsPath);
            $settings = array_merge($settings, json_decode($json, true) ?? []);
        }

        // 2. Cek eksistensi template dokumen
        $templates = [
            'permohonan' => 'templates/LSPRO_Draft_Permohonan.docx',
            'tinjauan' => 'templates/LSPRO_Draft_Tinjauan.docx',
            'kebijakan' => 'templates/LSPRO_Kebijakan_Mutu.pdf',
        ];

        $templateStatus = [];
        foreach ($templates as $key => $path) {
            $exists = Storage::disk('local')->exists($path);
            $templateStatus[$key] = [
                'exists' => $exists,
                'updated_at' => $exists ? Storage::disk('local')->lastModified($path) : null,
                'filename' => basename($path)
            ];
        }

        return view('superadmin.settings', compact('settings', 'templateStatus'));
    }

    /**
     * Simpan Pengaturan Kebijakan
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'kebijakan_umum' => 'required|string',
        ]);

        // Keep other existing settings if there are any
        $settingsPath = storage_path('app/settings.json');
        $settings = [];
        if (File::exists($settingsPath)) {
            $json = File::get($settingsPath);
            $settings = json_decode($json, true) ?? [];
        }

        $settings['kebijakan_umum'] = $request->kebijakan_umum;

        File::put($settingsPath, json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->back()->with('success', 'Pengaturan Kebijakan Sistem berhasil diperbarui.');
    }

    /**
     * Upload Template Formulir Baru
     */
    public function uploadTemplate(Request $request)
    {
        $request->validate([
            'template_dokumen' => 'required|mimes:docx,pdf|max:5120', // Maks 5MB
            'jenis_dokumen' => 'required|string',
        ]);

        if ($request->hasFile('template_dokumen')) {
            $jenis = $request->jenis_dokumen;
            $filename = 'LSPRO_Draft_Permohonan.docx';
            
            if ($jenis === 'tinjauan') {
                $filename = 'LSPRO_Draft_Tinjauan.docx';
            } elseif ($jenis === 'kebijakan') {
                $filename = 'LSPRO_Kebijakan_Mutu.pdf';
            }
            
            // Timpa file template yang lama dengan nama yang ditentukan
            $request->file('template_dokumen')->storeAs('templates', $filename, 'local');
            return redirect()->back()->with('success', 'Template Dokumen ' . ucfirst($jenis) . ' berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah template.');
    }
}
