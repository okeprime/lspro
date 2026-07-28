<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phpWord = new \PhpOffice\PhpWord\PhpWord();

$section = $phpWord->addSection();

$fontStyleName = 'rStyle';
$phpWord->addFontStyle($fontStyleName, array('name' => 'Arial', 'size' => 11, 'bold' => false));
$boldFontStyleName = 'bStyle';
$phpWord->addFontStyle($boldFontStyleName, array('name' => 'Arial', 'size' => 11, 'bold' => true));
$headerFontStyleName = 'hStyle';
$phpWord->addFontStyle($headerFontStyleName, array('name' => 'Arial', 'size' => 12, 'bold' => true));

$paragraphStyleName = 'pStyle';
$phpWord->addParagraphStyle($paragraphStyleName, array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH, 'spaceAfter' => 100));
$centerParagraphStyleName = 'cpStyle';
$phpWord->addParagraphStyle($centerParagraphStyleName, array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 100));

// Headers
$section->addText('KOP SURAT', $headerFontStyleName, $centerParagraphStyleName);
$section->addText('PERJANJIAN SERTIFIKASI', $headerFontStyleName, $centerParagraphStyleName);
$section->addText('Nomor : ${nomor_permohonan}', $fontStyleName, $centerParagraphStyleName);
$section->addTextBreak(1);

// Content
$section->addText('Pada hari ini ${hari_perjanjian}, tanggal ${tanggal_perjanjian} bulan ${bulan_perjanjian} tahun ${tahun_perjanjian}, kami yang bertanda tangan di bawah ini :', $fontStyleName, $paragraphStyleName);

$section->addText('a. Pihak Pertama, Balai Besar Pengujian Mutu dan Sertifikasi Obat Hewan (Contoh) yang beralamat di Jl. Tentara Pelajar No 12 Cimanggu, Bogor 16114 dan dalam hal ini diwakili oleh ${nama_tu} (ex officio Kepala BBPM SDLP) selaku Ketua Pihak Pertama.', $fontStyleName, $paragraphStyleName);

$section->addText('b. Pihak Kedua, ${nama_perusahaan} yang beralamat di ${alamat_perusahaan} dan dalam hal ini diwakili oleh ${nama_pemohon} selaku ${jabatan_pemohon}', $fontStyleName, $paragraphStyleName);

$section->addText('Pihak Pertama dan Pihak Kedua sepakat perjanjian ini dibuat dalam ruang lingkup proses sertifikasi kesesuaian oleh Pihak Pertama kepada Pihak Kedua. Pihak pertama memberikan sertifikat kepada pihak kedua yang menerima sertifikasi kesesuaian untuk lingkup sertifikasi sebagaimana tercantum dalam lampiran perjanjian ini.', $fontStyleName, $paragraphStyleName);

$section->addText('Apabila dalam pelaksanaan Perjanjian Bersama ini Para Pihak merasa perlu melakukan perubahan, maka perubahan tersebut hanya dapat dilakukan atas kesepakatan Para Pihak yang dituangkan dalam Addendum Perjanjian ini dan merupakan bagian yang tidak dapat dipisahkan dari Perjanjian ini.', $fontStyleName, $paragraphStyleName);

$section->addText('Untuk lingkup sertifikasi sebagaimana tercantum dalam lampiran perjanjian ini, dengan kondisi yang diuraikan pada ketentuan-ketentuan berikut.', $fontStyleName, $paragraphStyleName);

$section->addTextBreak(1);
$section->addText('Pasal 1', $boldFontStyleName, $centerParagraphStyleName);
$section->addText('Maksud dan Tujuan', $boldFontStyleName, $centerParagraphStyleName);
$section->addText('Maksud dan Tujuan perjanjian ini adalah untuk menunjang pelaksanaan Sertifikasi Produk Penggunaan Tanda Kesesuaian SNI produk ${nama_produk} di ${nama_perusahaan} sesuai yang dipersyaratkan oleh regulasi.', $fontStyleName, $paragraphStyleName);

$section->addTextBreak(1);
$section->addText('Pasal 2', $boldFontStyleName, $centerParagraphStyleName);
$section->addText('Ruang Lingkup', $boldFontStyleName, $centerParagraphStyleName);
$section->addText('2.1 Pihak Kedua sepakat untuk mensertifikasikan produknya dengan ruang lingkup SNI ${no_sni} tentang ${judul_sni} sesuai dengan tipe skema Sertifikasi Tipe 5 mengacu pada dokumen regulasi yang berlaku.', $fontStyleName, $paragraphStyleName);
$section->addText('2.2 Atas permintaan Pihak Kedua, Pihak Pertama dengan ini sepakat untuk melakukan jasa sertifikasi produk Pihak Kedua atas dasar Standard Nasional Indonesia/SNI terkait guna memperoleh sertifikat produk penggunaan tanda kesesuaian SNI berdasarkan syarat dan aturan sebagaimana diatur dalam perjanjian ini.', $fontStyleName, $paragraphStyleName);

$section->addTextBreak(1);
$section->addText('Pasal 3', $boldFontStyleName, $centerParagraphStyleName);
$section->addText('Hak dan kewajiban', $boldFontStyleName, $centerParagraphStyleName);
$section->addText('3.1 Pihak Kedua setuju untuk menjaga dan mengendalikan kesesuaian produk yang diproduksi dan dipasok olehnya dan telah disertifikasi oleh Pihak Pertama terhadap persyaratan yang ditetapkan dalam standar yang dituliskan dalam perjanjian sertifikasi kesesuaian, sesuai dengan ketentuan umum sertifikasi produk serta aturan khusus yang dinyatakan dalam Dokumen Perjanjian Sertifikasi Kesesuaian termasuk menerapkan perubahan yang sesuai bila perubahan tersebut telah dikomunikasikan oleh Pihak Pertama.', $fontStyleName, $paragraphStyleName);
$section->addText('3.2 Pihak Kedua setuju bahwa personel yang mewakili Pihak Pertama memiliki akses dan tidak dihalangi untuk mengakses pabrik dan atau fasilitas produksi yang berkaitan dengan produk yang tercakup dalam Dokumen Perjanjian Sertifikasi Kesesuaian, dengan atau tanpa pemberitahuan terlebih dahulu selama jam kerja yang normal berlaku pada fasilitas tersebut.', $fontStyleName, $paragraphStyleName);
$section->addText('3.3 Pihak Kedua setuju bahwa produk sebagaimana dimaksud dalam sertifikat kesesuaian akan diproduksi sesuai dengan spesifikasi yang sama dengan contoh atau sampel produk yang telah diperiksa dan diuji serta dinyatakan memenuhi standar yang diacu oleh Pihak Pertama.', $fontStyleName, $paragraphStyleName);
$section->addText('3.4 Pihak Kedua setuju untuk:', $fontStyleName, $paragraphStyleName);
$section->addText('(a) Setiap saat memenuhi Dokumen Perjanjian Sertifikasi Kesesuaian;', $fontStyleName, $paragraphStyleName);
$section->addText('(b) Hanya mengklaim bahwa produknya telah disertifikasi sesuai dengan ruang lingkup Sertifikasi Kesesuaian yang dimilikinya;', $fontStyleName, $paragraphStyleName);
$section->addText('(c) Menerima pengawasan berkala melalui surveilan setiap tahun;', $fontStyleName, $paragraphStyleName);
$section->addText('(d) Tidak menggunakan Sertifikat Kesesuaian dalam suatu cara yang merusak reputasi Sertifikasi Kesesuaian, dan tidak diperbolehkan untuk membuat pernyataan yang dipertimbangkan oleh Pihak Pertama Kementerian Pertanian adalah tidak benar, serta harus segera mengambil langkah-langkah untuk memperbaiki penggunaan/pernyataan yang tidak benar;', $fontStyleName, $paragraphStyleName);
$section->addText('(e) Dalam hal terjadi pembatalan dan pencabutan Sertifikat Kesesuaian pada produknya dan mencabut seluruh bahan iklan yang berisikan pengacuan ke Sertifikasi Kesesuaian SNI;', $fontStyleName, $paragraphStyleName);
$section->addText('(f) Menjelaskan dalam seluruh kontraknya dengan pelanggan bahwa Sertifikat Kesesuaian yang dimilikinya tidak dapat dianggap sebagai sesuatu yang mengurangi tanggung jawab kontrak antara Pihak Kedua dan pelanggannya dalam memasok produk yang konsisten sesuai standar.', $fontStyleName, $paragraphStyleName);

$section->addTextBreak(1);
$section->addText('Pasal 4', $boldFontStyleName, $centerParagraphStyleName);
$section->addText('Surveilan', $boldFontStyleName, $centerParagraphStyleName);
$section->addText('4.1 Pihak Pertama melaksanakan surveilan setiap tahun untuk mengetahui apakah Pihak Kedua melaksanakan kewajibannya sesuai dengan ketentuan umum sertifikasi produk dan ketentuan khusus skema sertifikasi produk, sebagaimana dinyatakan dalam Dokumen Perjanjian Sertifikasi Kesesuaian.', $fontStyleName, $paragraphStyleName);
$section->addText('4.2 Tidak terlaksananya surveilan setiap tahun akibat keengganan Pihak Kedua dapat mengakibatkan pembekuan sertifikat kesesuaian dan pencabutan sertifikat kesesuaian.', $fontStyleName, $paragraphStyleName);
$section->addText('4.3 Pihak Pertama mengirimkan pemberitahuan tentang surveilan kepada Pihak Kedua satu bulan sebelum tanggal surveilen.', $fontStyleName, $paragraphStyleName);
$section->addText('4.4 Pihak Pertama menerbitkan surat peringatan apabila Pihak Kedua tidak memberikan tanggapan dalam jangka waktu satu bulan terhitung tanggal surat pemberitahuan.', $fontStyleName, $paragraphStyleName);

$section->addTextBreak(1);
$section->addText('Pasal 16', $boldFontStyleName, $centerParagraphStyleName);
$section->addText('Penutup', $boldFontStyleName, $centerParagraphStyleName);
$section->addText('Perjanjian ini berlaku dimulai dari ditandatangani perjanjian ini sampai dengan masa sertifikasi dan dibuat dua rangkap, bermaterai cukup, masing-masing mempunyai kekuatan hukum yang sama, satu rangkap untuk PIHAK PERTAMA, dan satu rangkap untuk PIHAK KEDUA, dan masing-masing pihak dapat memperbanyak salinannya sesuai keperluan.', $fontStyleName, $paragraphStyleName);

$section->addTextBreak(3);

$tableStyle = array('borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 50);
$phpWord->addTableStyle('ttdTable', $tableStyle);
$table = $section->addTable('ttdTable');

$table->addRow();
$cell1 = $table->addCell(5000);
$cell1->addText('Atas nama Pihak Pertama', $fontStyleName, $centerParagraphStyleName);
$cell1->addText('Ketua LS Pro / Penanggung Jawab', $fontStyleName, $centerParagraphStyleName);
$cell1->addTextBreak(4);
$cell1->addText('( ${nama_tu} )', $boldFontStyleName, $centerParagraphStyleName);

$cell2 = $table->addCell(5000);
$cell2->addText('Atas nama Pihak Kedua', $fontStyleName, $centerParagraphStyleName);
$cell2->addText('Materai Rp 10.000,-', $fontStyleName, $centerParagraphStyleName);
$cell2->addTextBreak(4);
$cell2->addText('( ${nama_pemohon} )', $boldFontStyleName, $centerParagraphStyleName);

$dir = storage_path('app/templates');
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$file = $dir . '/Form_7.2-3_Perjanjian.docx';
$phpWord->save($file, 'Word2007');

echo "Template created at $file\n";
