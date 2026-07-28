<?php
// Replace visual labels 'TU' and 'Tata Usaha' with 'Administrasi'
// Replace sub_role 'tatausaha' and 'audit' with 'layanan' for database and codebase

$directories = [
    __DIR__ . '/resources/views',
    __DIR__ . '/app/Http/Controllers',
    __DIR__ . '/routes',
];

function processDir($dir) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            processDir($path);
        } elseif (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $content = file_get_contents($path);
            $original = $content;

            // 1. Text Replacements for UI
            $content = preg_replace('/\bTU\b/', 'Administrasi', $content);
            $content = str_ireplace('Tata Usaha', 'Administrasi', $content);
            
            // 2. sub_role replacements (except we don't want to break the word 'audit' in routes/views, 
            // so we'll be careful). 
            // In UserController:
            $content = str_replace("'tatausaha'", "'layanan'", $content);
            $content = str_replace('"tatausaha"', '"layanan"', $content);
            
            if ($content !== $original) {
                file_put_contents($path, $content);
                echo "Updated: $path\n";
            }
        }
    }
}

foreach ($directories as $dir) {
    processDir($dir);
}

echo "Done.\n";
