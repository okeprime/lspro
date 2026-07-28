<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\FormField;
use Illuminate\Http\Request;

class FormBuilderController extends Controller
{
    /**
     * Helper untuk mendapatkan daftar formulir yang butuh Form Builder Dinamis
     */
    public static function getDynamicFormTypes()
    {
        return [
            'permohonan' => [
                'title'       => 'Form 7.2-1 - Daftar Isian Permohonan',
                'description' => 'Formulir pengajuan sertifikasi awal yang diisi oleh Klien.',
                'icon'        => 'fa-solid fa-file-signature',
                'color'       => '#0d6efd',
                'filename'    => 'Form_7.2-1_Permohonan.docx',
            ],
            'perjanjian' => [
                'title'       => 'Form 7.2-3 - Perjanjian Sertifikasi',
                'description' => 'Form perjanjian yang diterbitkan LSPRO untuk ditandatangani Klien.',
                'icon'        => 'fa-solid fa-handshake',
                'color'       => '#198754',
                'filename'    => 'Form 7.2-3_LS Pro - Perjanjian Sertifikasi.docx',
            ],
            'ceklis_kelengkapan' => [
                'title'       => 'Form 7.2-4 - Ceklis Kelengkapan',
                'description' => 'Ceklis verifikasi dokumen yang diisi oleh Admin TU.',
                'icon'        => 'fa-solid fa-list-check',
                'color'       => '#0dcaf0',
                'filename'    => 'Form 7.2-4_LS Pro - Kelengkapan dan Kebenaran Dokumen Permohonan Sertifikasi.docx',
            ],
            'rencana_audit' => [
                'title'       => 'Form 7.4-1 - Rencana Audit',
                'description' => 'Rencana audit yang disusun oleh Lead Auditor.',
                'icon'        => 'fa-solid fa-calendar-check',
                'color'       => '#6f42c1',
                'filename'    => 'F 7.4-1_LS Pro - Rencana Audit, Surveilen.docx',
            ],
            'laporan_ketidaksesuaian' => [
                'title'       => 'Form 7.4-11 - Laporan Ketidaksesuaian',
                'description' => 'Laporan temuan ketidaksesuaian (NC) oleh Auditor.',
                'icon'        => 'fa-solid fa-triangle-exclamation',
                'color'       => '#dc3545',
                'filename'    => 'Form 7.4-11_LS Pro - Laporan Ketidaksesuaian.docx',
            ],
        ];
    }

    /**
     * Landing page: Pilih formulir mana yang mau di-edit
     */
    public function index(Request $request)
    {
        $formTypes = self::getDynamicFormTypes();

        // Jika ada query ?type=xxx, tampilkan editor field untuk tipe tersebut
        if ($request->has('type') && array_key_exists($request->type, $formTypes)) {
            $type = $request->type;
            $typeMeta = $formTypes[$type];
            $fields = FormField::where('form_type', $type)->orderBy('order_index')->get();
            return view('superadmin.form_builder.editor', compact('fields', 'type', 'typeMeta'));
        }

        // Hitung jumlah field per type
        $counts = FormField::selectRaw('form_type, count(*) as total')->groupBy('form_type')->pluck('total', 'form_type');
        return view('superadmin.form_builder.index', compact('formTypes', 'counts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'form_type'   => 'required|string',
            'section'     => 'required|string',
            'label'       => 'required|string',
            'name'        => 'required|string|unique:form_fields,name|regex:/^[a-z0-9_]+$/',
            'type'        => 'required|in:text,textarea,number,email,date,select,radio,file',
            'options'     => 'nullable|string',
            'order_index' => 'required|integer'
        ]);

        $data = $request->all();
        $data['is_required'] = $request->has('is_required');

        if ($request->options) {
            $data['options'] = array_map('trim', explode(',', $request->options));
        }

        FormField::create($data);

        return redirect()->route('superadmin.form_builder.index', ['type' => $request->form_type])
            ->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $field = FormField::findOrFail($id);

        $request->validate([
            'section'     => 'required|string',
            'label'       => 'required|string',
            'name'        => 'required|string|regex:/^[a-z0-9_]+$/|unique:form_fields,name,' . $id,
            'type'        => 'required|in:text,textarea,number,email,date,select,radio,file',
            'options'     => 'nullable|string',
            'order_index' => 'required|integer'
        ]);

        $data = $request->all();
        $data['is_required'] = $request->has('is_required');

        if ($request->options) {
            $data['options'] = array_map('trim', explode(',', $request->options));
        } else {
            $data['options'] = null;
        }

        $field->update($data);

        return redirect()->route('superadmin.form_builder.index', ['type' => $field->form_type])
            ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $field = FormField::findOrFail($id);
        $type = $field->form_type;
        $field->delete();

        return redirect()->route('superadmin.form_builder.index', ['type' => $type])
            ->with('success', 'Pertanyaan berhasil dihapus.');
    }

    /**
     * Upload template .docx baru untuk spesifik form_type
     */
    public function uploadTemplate(Request $request, $form_type)
    {
        $request->validate([
            'template_dokumen' => 'required|file|mimes:docx|max:5120' // max 5MB
        ]);

        $formTypes = self::getDynamicFormTypes();
        if (!array_key_exists($form_type, $formTypes)) {
            return back()->with('error', 'Tipe formulir tidak valid.');
        }

        $filename = $formTypes[$form_type]['filename'];
        $path = storage_path('app/templates');

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $file = $request->file('template_dokumen');
        // Pindahkan dan timpa file dengan nama asli dari konfigurasi
        $file->move($path, $filename);

        return back()->with('success', 'Template dokumen untuk form ' . $formTypes[$form_type]['title'] . ' berhasil diperbarui.');
    }
}
