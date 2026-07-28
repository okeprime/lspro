<?php
$zip = new ZipArchive;
$file = 'storage/app/templates/Form_7.2-1_Permohonan.docx';
if ($zip->open($file) === TRUE) {
    $docXml = $zip->getFromName('word/document.xml');
    preg_match_all('/<w:t(?:.*?)>([^<]*)<\/w:t>/', $docXml, $matches);
    foreach($matches[1] as $m) {
        if (stripos($m, 'KOP') !== false || stripos($m, 'PERUSAHAAN') !== false) {
            echo $m . "\n";
        }
    }
}
