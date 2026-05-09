<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Firebase\JWT\JWT;

$secret = config('billing.jwt_secret');
$token = JWT::encode([
    'uid' => 'fcb57224-a2e7-4a59-b6cf-0b0704899ca5',
    'email' => 'admin@gmail.com',
    'role' => 'superadmin',
    'roleKey' => 'owner',
    'permissions' => ['access_admin_app','view_finance_totals','view_finance_reports','collect_customer_payment','view_customer_bill_amount','manage_settings','manage_settings_wa','manage_users','manage_backup_audit','manage_tasks'],
], $secret, 'HS256');

$httpKernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Test 1: Auth Me
$r = Illuminate\Http\Request::create('/api/auth/me', 'GET');
$r->headers->set('Authorization', "Bearer $token");
$resp = $httpKernel->handle($r);
echo "=== /api/auth/me ===\n";
echo "Status: " . $resp->getStatusCode() . "\n";
echo "Body: " . substr($resp->getContent(), 0, 500) . "\n\n";

// Test 2: Pelanggan Master List
$r2 = Illuminate\Http\Request::create('/api/pelanggan/master-list', 'GET');
$r2->headers->set('Authorization', "Bearer $token");
$resp2 = $httpKernel->handle($r2);
echo "=== /api/pelanggan/master-list ===\n";
echo "Status: " . $resp2->getStatusCode() . "\n";
$data2 = json_decode($resp2->getContent(), true);
echo "Count: " . (is_array($data2) ? count($data2) : 'N/A') . "\n";
if (is_array($data2) && count($data2) > 0) {
    echo "First: " . json_encode($data2[0], JSON_UNESCAPED_UNICODE) . "\n";
}
echo "\n";

// Test 3: POST collections/areas with JSON body
$r3 = Illuminate\Http\Request::create('/api/collections/areas', 'POST', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
    'HTTP_AUTHORIZATION' => "Bearer $token",
], json_encode(['nama' => 'Area Test Browser', 'keterangan' => 'Tes area baru']));
$resp3 = $httpKernel->handle($r3);
echo "=== POST /api/collections/areas (JSON body) ===\n";
echo "Status: " . $resp3->getStatusCode() . "\n";
echo "Body: " . $resp3->getContent() . "\n\n";

// Test 4: POST collections/pelanggan with JSON body
$pelData = [
    'idPelanggan' => 'PEL-TEST-002',
    'nama' => 'Pelanggan Test Baru',
    'noWA' => '081234567890',
    'area' => 'ROWO',
    'paket' => 'Basic 10Mbps',
    'hargaPaket' => 150000,
    'tglTagih' => 5,
    'alamat' => 'Jl. Test No. 2',
    'status' => 'aktif',
    'totalFinal' => 150000,
];
$r4 = Illuminate\Http\Request::create('/api/collections/pelanggan', 'POST', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
    'HTTP_AUTHORIZATION' => "Bearer $token",
], json_encode($pelData));
$resp4 = $httpKernel->handle($r4);
echo "=== POST /api/collections/pelanggan (JSON body) ===\n";
echo "Status: " . $resp4->getStatusCode() . "\n";
echo "Body: " . $resp4->getContent() . "\n\n";

// Test 5: POST collections/paket with JSON body
$r5 = Illuminate\Http\Request::create('/api/collections/paket', 'POST', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
    'HTTP_AUTHORIZATION' => "Bearer $token",
], json_encode(['nama' => 'Paket Test 30Mbps', 'harga' => 250000, 'deskripsi' => 'Internet 30 Mbps', 'aktif' => 1]));
$resp5 = $httpKernel->handle($r5);
echo "=== POST /api/collections/paket (JSON body) ===\n";
echo "Status: " . $resp5->getStatusCode() . "\n";
echo "Body: " . $resp5->getContent() . "\n\n";

echo "=== Done ===\n";
