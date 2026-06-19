<?php
/**
 * SETUP MANUAL — Generate APP_KEY tanpa artisan
 * Upload ke public_html/, buka di browser, lalu HAPUS.
 */

$projectPath = __DIR__ . '/../project-lspro';
$envFile = $projectPath . '/.env';

echo "<h2>🔧 LSPro Setup</h2>";

// Step 1: Generate APP_KEY
if (isset($_GET['action']) && $_GET['action'] === 'genkey') {
    $key = 'base64:' . base64_encode(random_bytes(32));
    $envContent = file_get_contents($envFile);
    
    if (strpos($envContent, 'APP_KEY=') !== false) {
        $envContent = preg_replace('/APP_KEY=.*/', 'APP_KEY=' . $key, $envContent);
    } else {
        $envContent .= "\nAPP_KEY=" . $key;
    }
    
    file_put_contents($envFile, $envContent);
    echo "<p style='color:green;font-weight:bold;'>✅ APP_KEY berhasil di-generate!</p>";
    echo "<p><b>Key:</b> " . $key . "</p>";
    echo "<p><a href='setup.php'>← Kembali</a></p>";
    exit;
}

// Step 2: Run artisan command
if (isset($_GET['action']) && $_GET['action'] === 'artisan' && isset($_GET['cmd'])) {
    $allowed = ['storage:link', 'config:cache', 'route:cache', 'view:cache', 'config:clear', 'cache:clear', 'optimize', 'optimize:clear'];
    $cmd = $_GET['cmd'];
    
    if (in_array($cmd, $allowed)) {
        $output = [];
        $code = 0;
        exec("cd " . escapeshellarg($projectPath) . " && php artisan " . $cmd . " 2>&1", $output, $code);
        echo "<p><b>Command:</b> php artisan " . htmlspecialchars($cmd) . "</p>";
        echo "<p><b>Result:</b> " . ($code === 0 ? '✅ Success' : '❌ Error (code: '.$code.')') . "</p>";
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    } else {
        echo "<p style='color:red;'>Command not allowed.</p>";
    }
    echo "<p><a href='setup.php'>← Kembali</a></p>";
    exit;
}

// Dashboard
echo "<p><b>.env path:</b> " . $envFile . "</p>";
echo "<p><b>.env exists:</b> " . (file_exists($envFile) ? 'YES ✅' : 'NO ❌') . "</p>";

// Read current APP_KEY
if (file_exists($envFile)) {
    $env = file_get_contents($envFile);
    preg_match('/APP_KEY=(.*)/', $env, $matches);
    $currentKey = trim($matches[1] ?? '');
    echo "<p><b>Current APP_KEY:</b> " . ($currentKey ?: '<span style="color:red;">(KOSONG - harus generate!)</span>') . "</p>";
}

echo "<hr>";
echo "<h3>Step 1: Generate APP_KEY</h3>";
echo "<p><a href='setup.php?action=genkey' style='display:inline-block;padding:10px 20px;background:#16a34a;color:white;text-decoration:none;border-radius:8px;font-weight:bold;'>🔑 Generate APP_KEY</a></p>";

echo "<h3>Step 2: Jalankan Artisan (setelah key di-generate)</h3>";
$commands = ['storage:link', 'config:cache', 'route:cache', 'view:cache'];
foreach ($commands as $cmd) {
    echo "<p><a href='setup.php?action=artisan&cmd=" . urlencode($cmd) . "' style='display:inline-block;padding:8px 16px;background:#334155;color:white;text-decoration:none;border-radius:6px;margin:4px;font-family:monospace;'>php artisan " . $cmd . "</a></p>";
}

echo "<hr>";
echo "<p style='color:red;font-weight:bold;'>⚠️ HAPUS file setup.php setelah selesai!</p>";
