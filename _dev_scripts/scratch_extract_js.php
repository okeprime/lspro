<?php
$html = file_get_contents('resources/views/pengajuan/create_otomatis.blade.php');
preg_match('/<script>(.*?)<\/script>/is', $html, $matches);
if (isset($matches[1])) {
    echo substr($matches[1], 5000, 3000);
}
