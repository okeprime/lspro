<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FormField;

// Delete tipe_produk (we will replace with komoditas_pupuk)
FormField::where('name', 'tipe_produk')->delete();

// Komoditas Pupuk Options
$komoditas = [
    "Pupuk amonium sulfat (ZA) - SNI 02-1760-2005",
    "Pupuk kalium klorida (KCl) - SNI 02-2805-2005",
    "Pupuk SP-36 - SNI 02-3769-2005",
    "Pupuk fosfat alam untuk pertanian - SNI 02-3776-2005",
    "Pupuk urea - SNI 2801:2010",
    "Pupuk NPK Padat - SNI 2803:2024",
    "Kapur untuk pertanian - SNI 02-0482:1998",
    "Pupuk organik padat - SNI 7763:2018 dan SNI 7763:2024",
    "Pupuk dolomit - SNI 02-2804-2005",
    "Pupuk Kiserit - SNI 02-2807-1992"
];

FormField::updateOrCreate(
    ['name' => 'komoditas_pupuk', 'form_type' => 'permohonan'],
    [
        'section' => '4. Data Produk & SNI',
        'label' => 'Komoditas Pupuk',
        'type' => 'select',
        'options' => $komoditas,
        'is_required' => true,
        'order_index' => 18
    ]
);

FormField::updateOrCreate(
    ['name' => 'jarak_pabrik', 'form_type' => 'permohonan'],
    [
        'section' => '3. Legalitas & Data Pabrik',
        'label' => 'Jarak Lokasi Pabrik / Penjemputan Sampel (KM)',
        'type' => 'number',
        'options' => null,
        'is_required' => true,
        'order_index' => 15
    ]
);

FormField::updateOrCreate(
    ['name' => 'ada_maklon', 'form_type' => 'permohonan'],
    [
        'section' => '3. Legalitas & Data Pabrik',
        'label' => 'Apakah Menggunakan Maklon?',
        'type' => 'select',
        'options' => ['Tidak', 'Ya'],
        'is_required' => true,
        'order_index' => 16
    ]
);

FormField::updateOrCreate(
    ['name' => 'nama_maklon', 'form_type' => 'permohonan'],
    [
        'section' => '3. Legalitas & Data Pabrik',
        'label' => 'Nama Maklon',
        'type' => 'text',
        'options' => null,
        'is_required' => false,
        'order_index' => 17
    ]
);

FormField::updateOrCreate(
    ['name' => 'sketsa_logo', 'form_type' => 'permohonan'],
    [
        'section' => '4. Data Produk & SNI',
        'label' => 'Upload Sketsa Posisi Logo (Depan, Belakang, Kanan, Kiri) [Format Gambar/PDF]',
        'type' => 'file',
        'options' => null,
        'is_required' => true,
        'order_index' => 20
    ]
);

echo "Fields updated successfully.\n";
