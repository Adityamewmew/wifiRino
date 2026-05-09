<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Setup session
$session = $app['session.store'];
$session->put('staff_user', [
    'id' => 'fcb57224-a2e7-4a59-b6cf-0b0704899ca5',
    'uid' => 'fcb57224-a2e7-4a59-b6cf-0b0704899ca5',
    'email' => 'admin@gmail.com',
    'nama' => 'Administrator',
    'role' => 'superadmin',
    'roleKey' => 'owner',
    'permissions' => ['access_admin_app','view_finance_totals','manage_settings'],
    'aktif' => 1,
    'areas' => [],
]);
$session->put('_token', 'test_token');

// Test POST pelanggan
echo "=== Test: Tambah Pelanggan ===\n";
$request = Illuminate\Http\Request::create('/pelanggan', 'POST', [
    '_token' => 'test_token',
    'nama' => 'Pelanggan Test PHP',
    'noWA' => '081234000000',
    'area' => 'SELOGIRI',
    'paket' => 'Home Service - 150 k',
    'hargaPaket' => 150000,
    'tglTagih' => 15,
    'alamat' => 'Jl. Testing PHP No. 1',
    'status' => 'aktif',
    'totalFinal' => 150000,
    'tanggalMulaiStr' => '2026-05-01',
]);
$request->setLaravelSession($session);
$resp = $kernel->handle($request);
echo "Status: " . $resp->getStatusCode() . "\n";
if ($resp->getStatusCode() === 302) {
    echo "Redirect to: " . $resp->headers->get('Location') . "\n";
    $flashSession = $session->get('_flash.new', []);
    echo "Flash session data: " . json_encode($session->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "Body: " . substr($resp->getContent(), 0, 500) . "\n";
}

// Test POST area
echo "\n=== Test: Tambah Area ===\n";
$request2 = Illuminate\Http\Request::create('/area', 'POST', [
    '_token' => 'test_token',
    'nama' => 'AREA TEST PHP',
    'keterangan' => 'Area baru dari PHP test',
]);
$request2->setLaravelSession($session);
$resp2 = $kernel->handle($request2);
echo "Status: " . $resp2->getStatusCode() . "\n";
if ($resp2->getStatusCode() === 302) {
    echo "Redirect to: " . $resp2->headers->get('Location') . "\n";
    echo "✅ Area berhasil ditambahkan!\n";
}

// Test POST paket
echo "\n=== Test: Tambah Paket ===\n";
$request3 = Illuminate\Http\Request::create('/paket', 'POST', [
    '_token' => 'test_token',
    'nama' => 'Paket Test PHP 50Mbps',
    'harga' => 300000,
    'deskripsi' => 'Internet 50 Mbps dari PHP test',
    'aktif' => 1,
]);
$request3->setLaravelSession($session);
$resp3 = $kernel->handle($request3);
echo "Status: " . $resp3->getStatusCode() . "\n";
if ($resp3->getStatusCode() === 302) {
    echo "Redirect to: " . $resp3->headers->get('Location') . "\n";
    echo "✅ Paket berhasil ditambahkan!\n";
}

// Verify data was inserted
echo "\n=== Verifikasi Data ===\n";
$pelCount = \Illuminate\Support\Facades\DB::table('pelanggan')->where('nama', 'Pelanggan Test PHP')->count();
echo "Pelanggan 'Test PHP' count: $pelCount\n";
$areaCount = \Illuminate\Support\Facades\DB::table('areas')->where('nama', 'AREA TEST PHP')->count();
echo "Area 'AREA TEST PHP' count: $areaCount\n";
$paketCount = \Illuminate\Support\Facades\DB::table('paket')->where('nama', 'Paket Test PHP 50Mbps')->count();
echo "Paket 'Paket Test PHP 50Mbps' count: $paketCount\n";

echo "\n=== CRUD Tests Complete ===\n";
