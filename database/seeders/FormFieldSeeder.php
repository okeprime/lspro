<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            // 1. Identitas Pemohon
            ['section' => '1. Identitas Pemohon', 'label' => 'Nama Pemohon', 'name' => 'nama_pemohon', 'type' => 'text', 'is_required' => true, 'order_index' => 1],
            ['section' => '1. Identitas Pemohon', 'label' => 'Jabatan Pemohon', 'name' => 'jabatan_pemohon', 'type' => 'text', 'is_required' => true, 'order_index' => 2],
            ['section' => '1. Identitas Pemohon', 'label' => 'Alamat Pemohon', 'name' => 'alamat_pemohon', 'type' => 'textarea', 'is_required' => true, 'order_index' => 3],
            ['section' => '1. Identitas Pemohon', 'label' => 'No. Telepon', 'name' => 'telp_pemohon', 'type' => 'text', 'is_required' => true, 'order_index' => 4],
            ['section' => '1. Identitas Pemohon', 'label' => 'No. HP', 'name' => 'hp_pemohon', 'type' => 'text', 'is_required' => true, 'order_index' => 5],
            ['section' => '1. Identitas Pemohon', 'label' => 'Kewarganegaraan', 'name' => 'kewarganegaraan_pemohon', 'type' => 'text', 'is_required' => true, 'order_index' => 6],
            ['section' => '1. Identitas Pemohon', 'label' => 'Status Pemohon', 'name' => 'status_pemohon', 'type' => 'select', 'options' => ['Pemilik', 'Kuasa dari Pemilik'], 'is_required' => true, 'order_index' => 7],

            // 2. Identitas Perusahaan
            ['section' => '2. Identitas Perusahaan & Penghubung', 'label' => 'Nama Penghubung', 'name' => 'nama_penghubung', 'type' => 'text', 'is_required' => true, 'order_index' => 8],
            ['section' => '2. Identitas Perusahaan & Penghubung', 'label' => 'Jabatan Penghubung', 'name' => 'jabatan_penghubung', 'type' => 'text', 'is_required' => false, 'order_index' => 9],
            ['section' => '2. Identitas Perusahaan & Penghubung', 'label' => 'Nama Perusahaan', 'name' => 'nama_perusahaan', 'type' => 'text', 'is_required' => true, 'order_index' => 10],
            ['section' => '2. Identitas Perusahaan & Penghubung', 'label' => 'Alamat Kantor/Perusahaan', 'name' => 'alamat_kantor', 'type' => 'textarea', 'is_required' => true, 'order_index' => 11],
            ['section' => '2. Identitas Perusahaan & Penghubung', 'label' => 'Kota Kantor', 'name' => 'kota_kantor', 'type' => 'text', 'is_required' => false, 'order_index' => 12],
            ['section' => '2. Identitas Perusahaan & Penghubung', 'label' => 'No. Telepon Kantor', 'name' => 'telp_kantor', 'type' => 'text', 'is_required' => false, 'order_index' => 13],

            // 3. Legalitas & Pabrik
            ['section' => '3. Legalitas & Data Pabrik', 'label' => 'Alamat Pabrik', 'name' => 'alamat_pabrik', 'type' => 'textarea', 'is_required' => true, 'order_index' => 14],
            ['section' => '3. Legalitas & Data Pabrik', 'label' => 'Kota Pabrik', 'name' => 'kota_pabrik', 'type' => 'text', 'is_required' => false, 'order_index' => 15],
            
            // 4. Data Produk & SNI
            ['section' => '4. Data Produk & SNI', 'label' => 'Nama Produk', 'name' => 'nama_produk', 'type' => 'text', 'is_required' => true, 'order_index' => 16],
            ['section' => '4. Data Produk & SNI', 'label' => 'Merek Produk', 'name' => 'merek_produk', 'type' => 'text', 'is_required' => true, 'order_index' => 17],
            ['section' => '4. Data Produk & SNI', 'label' => 'Tipe / Jenis Produk', 'name' => 'tipe_produk', 'type' => 'text', 'is_required' => true, 'order_index' => 18],
            ['section' => '4. Data Produk & SNI', 'label' => 'Nomor SNI', 'name' => 'no_sni', 'type' => 'text', 'is_required' => true, 'order_index' => 19],
            ['section' => '4. Data Produk & SNI', 'label' => 'Kapasitas Produksi / Tahun', 'name' => 'kapasitas_produksi', 'type' => 'text', 'is_required' => true, 'order_index' => 20],
            ['section' => '4. Data Produk & SNI', 'label' => 'Standar Sistem Mutu yang Digunakan', 'name' => 'standar_smm', 'type' => 'text', 'is_required' => true, 'order_index' => 21],

            // 5. Tenaga Kerja
            ['section' => '5. Jumlah Tenaga Kerja', 'label' => 'Total Tenaga Kerja', 'name' => 'total_tk', 'type' => 'number', 'is_required' => true, 'order_index' => 22],
            ['section' => '5. Jumlah Tenaga Kerja', 'label' => 'Bagian Produksi', 'name' => 'tk_produksi', 'type' => 'number', 'is_required' => true, 'order_index' => 23],
            ['section' => '5. Jumlah Tenaga Kerja', 'label' => 'Bagian Pengendalian Mutu (QC)', 'name' => 'tk_mutu', 'type' => 'number', 'is_required' => true, 'order_index' => 24],
            ['section' => '5. Jumlah Tenaga Kerja', 'label' => 'Bagian Staf (Kantor)', 'name' => 'tk_staf', 'type' => 'number', 'is_required' => true, 'order_index' => 25],
            ['section' => '5. Jumlah Tenaga Kerja', 'label' => 'Bagian Non-Staf', 'name' => 'tk_nonstaf', 'type' => 'number', 'is_required' => true, 'order_index' => 26],
        ];

        foreach ($fields as $field) {
            \App\Models\FormField::create($field);
        }
    }
}
