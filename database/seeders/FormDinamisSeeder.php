<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormField;

class FormDinamisSeeder extends Seeder
{
    public function run()
    {
        // Data untuk Form Perjanjian Sertifikasi
        $perjanjianFields = [
            [
                'form_type' => 'perjanjian',
                'section' => 'Data Pihak Pertama (LSPRO)',
                'name' => 'nama_pihak_pertama',
                'label' => 'Nama Kepala LSPro',
                'type' => 'text',
                'is_required' => true,
                'order_index' => 1,
            ],
            [
                'form_type' => 'perjanjian',
                'section' => 'Data Pihak Kedua (Klien)',
                'name' => 'nama_pihak_kedua',
                'label' => 'Nama Pimpinan Klien',
                'type' => 'text',
                'is_required' => true,
                'order_index' => 2,
            ],
            [
                'form_type' => 'perjanjian',
                'section' => 'Detail Sertifikasi',
                'name' => 'biaya_sertifikasi',
                'label' => 'Biaya Sertifikasi (Rp)',
                'type' => 'number',
                'is_required' => true,
                'order_index' => 3,
            ],
            [
                'form_type' => 'perjanjian',
                'section' => 'Detail Sertifikasi',
                'name' => 'tanggal_perjanjian',
                'label' => 'Tanggal Perjanjian',
                'type' => 'date',
                'is_required' => true,
                'order_index' => 4,
            ]
        ];

        // Data untuk Form Rencana Audit
        $rencanaAuditFields = [
            [
                'form_type' => 'rencana_audit',
                'section' => 'Informasi Audit',
                'name' => 'lead_auditor',
                'label' => 'Nama Lead Auditor',
                'type' => 'text',
                'is_required' => true,
                'order_index' => 1,
            ],
            [
                'form_type' => 'rencana_audit',
                'section' => 'Informasi Audit',
                'name' => 'tanggal_audit',
                'label' => 'Tanggal Pelaksanaan Audit',
                'type' => 'date',
                'is_required' => true,
                'order_index' => 2,
            ],
            [
                'form_type' => 'rencana_audit',
                'section' => 'Informasi Audit',
                'name' => 'lokasi_audit',
                'label' => 'Lokasi Pabrik / Audit',
                'type' => 'textarea',
                'is_required' => true,
                'order_index' => 3,
            ]
        ];

        // Hapus yang sudah ada (optional) untuk test
        FormField::where('form_type', 'perjanjian')->delete();
        FormField::where('form_type', 'rencana_audit')->delete();

        foreach ($perjanjianFields as $field) {
            FormField::create($field);
        }

        foreach ($rencanaAuditFields as $field) {
            FormField::create($field);
        }
    }
}
