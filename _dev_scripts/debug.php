<?php
echo "<h3>Path Debug - LSPro Hostinger</h3>";
echo "<b>__DIR__:</b> " . __DIR__ . "<br>";
echo "<b>Project path:</b> " . __DIR__ . '/../project-lspro' . "<br>";
echo "<b>Project exists:</b> " . (file_exists(__DIR__ . '/../project-lspro') ? 'YES ✅' : 'NO ❌') . "<br>";
echo "<b>Vendor exists:</b> " . (file_exists(__DIR__ . '/../project-lspro/vendor/autoload.php') ? 'YES ✅' : 'NO ❌') . "<br>";
echo "<b>.env exists:</b> " . (file_exists(__DIR__ . '/../project-lspro/.env') ? 'YES ✅' : 'NO ❌') . "<br>";
echo "<b>Storage writable:</b> " . (is_writable(__DIR__ . '/../project-lspro/storage') ? 'YES ✅' : 'NO ❌') . "<br>";
echo "<b>PHP version:</b> " . phpversion() . "<br>";
echo "<h3>Parent dir contents:</h3><pre>";
print_r(scandir(__DIR__ . '/..'));
echo "</pre>";
