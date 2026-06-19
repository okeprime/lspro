<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'superadmin@lspro.local')->first();
if ($user) {
    $user->password = \Illuminate\Support\Facades\Hash::make('password');
    $user->save();
    echo "Password reset for " . $user->email . "\n";
} else {
    echo "User not found\n";
}
