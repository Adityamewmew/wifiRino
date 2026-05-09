<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Simulate login first
$loginGetReq = Illuminate\Http\Request::create('/login', 'GET');
$loginResp = $kernel->handle($loginGetReq);
// Extract session cookie from response
$sessionCookies = $loginResp->headers->getCookies();
echo "Login page status: " . $loginResp->getStatusCode() . "\n";

// Simulate staff session by directly setting session data
$request = Illuminate\Http\Request::create('/dashboard-admin', 'GET');
$request->setLaravelSession($app['session.store']);
$app['session.store']->put('staff_user', [
    'id' => 'fcb57224-a2e7-4a59-b6cf-0b0704899ca5',
    'uid' => 'fcb57224-a2e7-4a59-b6cf-0b0704899ca5',
    'email' => 'admin@gmail.com',
    'nama' => 'Administrator',
    'role' => 'superadmin',
    'roleKey' => 'owner',
    'permissions' => ['access_admin_app','view_finance_totals','view_finance_reports','collect_customer_payment','view_customer_bill_amount','manage_settings','manage_settings_wa','manage_users','manage_backup_audit','manage_tasks'],
    'aktif' => 1,
    'areas' => [],
]);

$resp = $kernel->handle($request);
echo "Dashboard status: " . $resp->getStatusCode() . "\n";
$html = $resp->getContent();
echo "Dashboard length: " . strlen($html) . "\n";

if (str_contains($html, 'Ringkasan Eksekutif')) {
    echo "✅ NEW Dashboard view is rendering correctly!\n";
} else {
    echo "❌ New Dashboard NOT found\n";
}

if (str_contains($html, 'Pelanggan Aktif')) echo "✅ Stat: Pelanggan Aktif found\n";
if (str_contains($html, 'Pelanggan Baru')) echo "✅ Stat: Pelanggan Baru found\n";
if (str_contains($html, 'Sudah Bayar')) echo "✅ Stat: Sudah Bayar found\n";
if (str_contains($html, 'Belum Bayar')) echo "✅ Stat: Belum Bayar found\n";

// Extract numbers
preg_match_all('/<p>(\d+)<\/p>/', $html, $nums);
if (!empty($nums[1])) {
    echo "Stats values: " . implode(', ', $nums[1]) . "\n";
}

// Test pelanggan page
$request2 = Illuminate\Http\Request::create('/pelanggan', 'GET');
$request2->setLaravelSession($app['session.store']);
$resp2 = $kernel->handle($request2);
echo "\nPelanggan status: " . $resp2->getStatusCode() . "\n";
$html2 = $resp2->getContent();

if (str_contains($html2, 'Master Data Pelanggan')) {
    echo "✅ NEW Pelanggan view is rendering correctly!\n";
} else {
    echo "❌ New Pelanggan NOT found\n";
}
if (preg_match('/Total: (\d+) Pelanggan/', $html2, $tm)) {
    echo "✅ Pelanggan count: " . $tm[1] . "\n";
}

// Test area page
$request3 = Illuminate\Http\Request::create('/area', 'GET');
$request3->setLaravelSession($app['session.store']);
$resp3 = $kernel->handle($request3);
echo "\nArea status: " . $resp3->getStatusCode() . "\n";
$html3 = $resp3->getContent();

if (str_contains($html3, 'Manajemen Area')) {
    echo "✅ NEW Area view is rendering correctly!\n";
} else {
    echo "❌ New Area NOT found\n";
}
if (preg_match('/Total: (\d+) Area/', $html3, $am)) {
    echo "✅ Area count: " . $am[1] . "\n";
}

// Test paket page
$request4 = Illuminate\Http\Request::create('/paket', 'GET');
$request4->setLaravelSession($app['session.store']);
$resp4 = $kernel->handle($request4);
echo "\nPaket status: " . $resp4->getStatusCode() . "\n";
$html4 = $resp4->getContent();

if (str_contains($html4, 'Manajemen Paket')) {
    echo "✅ NEW Paket view is rendering correctly!\n";
} else {
    echo "❌ New Paket NOT found\n";
}
if (preg_match('/Total: (\d+) Paket/', $html4, $pm)) {
    echo "✅ Paket count: " . $pm[1] . "\n";
}

echo "\n=== All Tests Complete ===\n";
