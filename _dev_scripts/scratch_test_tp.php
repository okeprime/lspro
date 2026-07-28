<?php
$zip = new ZipArchive;
$zip->open('storage/app/templates/Form_7.2-1_Permohonan.docx');
$xml = $zip->getFromName('word/document.xml');
echo (strpos($xml, 'KEMENTERIAN') !== false ? 'KEMENTERIAN EXISTS ' : 'NO KEMENTERIAN ');
echo (strpos($xml, 'KOP PERUSAHAAN') !== false ? 'KOP EXISTS ' : 'NO KOP ');
