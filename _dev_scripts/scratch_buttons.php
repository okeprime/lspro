<?php
$html = file_get_contents('resources/views/pengajuan/create_otomatis.blade.php');
preg_match_all('/<button[^>]*>.*?<\/button>/is', $html, $matches);
foreach($matches[0] as $i => $btn) echo $btn . "\n---\n";
