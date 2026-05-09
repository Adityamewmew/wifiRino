<?php
// Quick script to reset admin password for testing
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 10]);
DB::table('users')->where('email', 'admin@gmail.com')->update(['password' => $hash]);
echo "Password updated for admin@gmail.com => admin123\n";
echo "Hash: {$hash}\n";

// Verify it works
$row = DB::table('users')->where('email', 'admin@gmail.com')->first();
$ok = password_verify('admin123', $row->password);
echo "Verification: " . ($ok ? 'OK' : 'FAILED') . "\n";
