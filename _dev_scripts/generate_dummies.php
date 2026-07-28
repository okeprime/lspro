<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

$pdfDir = __DIR__ . '/public/uploads/Klausul 7';
$docxDir = __DIR__ . '/storage/app/templates';

if (!is_dir($docxDir)) {
    mkdir($docxDir, 0755, true);
}

$files = scandir($pdfDir);
$count = 0;

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf' || pathinfo($file, PATHINFO_EXTENSION) === 'xls') {
        $filenameWithoutExt = pathinfo($file, PATHINFO_FILENAME);
        $docxFilename = $filenameWithoutExt . '.docx';
        $docxPath = $docxDir . '/' . $docxFilename;

        // Skip if exists
        if (file_exists($docxPath)) {
            echo "Skipping (already exists): $docxFilename\n";
            continue;
        }

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        
        $section->addText("TEMPLATE DUMMY: " . $filenameWithoutExt, ['bold' => true, 'size' => 16, 'color' => '1e3a5f']);
        $section->addTextBreak(2);
        
        $section->addText("Ini adalah template dummy yang di-generate otomatis untuk keperluan digitalisasi web LSPRO.", ['italic' => true]);
        $section->addTextBreak(1);
        
        $section->addText("Berikut adalah contoh variabel placeholder yang nantinya akan diganti (autofill) oleh sistem berdasarkan inputan dari Form Builder:", ['bold' => true]);
        $section->addTextBreak(1);
        
        // Add some common placeholders
        $section->addText('Nama Perusahaan: ${nama_perusahaan}');
        $section->addText('Nama Pimpinan: ${nama_pimpinan}');
        $section->addText('Alamat: ${alamat_perusahaan}');
        $section->addText('Tanggal: ${tanggal}');
        
        $section->addTextBreak(2);
        $section->addText("Untuk menyesuaikan template ini dengan dokumen aslinya, silakan edit file .docx ini di Microsoft Word dan masukkan tabel, logo, atau teks asli, lalu letakkan variabel \${nama_variabel} pada bagian yang ingin diisi otomatis.");
        
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($docxPath);
        
        echo "Generated: $docxFilename\n";
        $count++;
    }
}

echo "\nSuccessfully generated $count dummy .docx templates in storage/app/templates!\n";
