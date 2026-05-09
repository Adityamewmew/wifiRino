<?php

use App\Http\Controllers\Web\Admin\AreaWebController;
use App\Http\Controllers\Web\Admin\BillingPageController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\PaketWebController;
use App\Http\Controllers\Web\Admin\PelangganWebController;
use App\Http\Controllers\Web\Admin\SetupController;
use App\Http\Controllers\Web\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest.staff')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
});

Route::post('/login', [LoginController::class, 'staffLogin'])->name('login.staff');
Route::post('/login/portal', [LoginController::class, 'portalLogin'])->name('login.portal');

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/setup-admin', [SetupController::class, 'show'])->name('setup.admin');

// ============================================================
// Staff routes — server-side rendered (PHP/Blade)
// ============================================================
Route::middleware('staff.session')->group(function () {

    // Dashboard
    Route::get('/dashboard-admin', [DashboardController::class, 'index']);

    // Pelanggan CRUD
    Route::get('/pelanggan', [PelangganWebController::class, 'index']);
    Route::post('/pelanggan', [PelangganWebController::class, 'store']);
    Route::put('/pelanggan/{id}', [PelangganWebController::class, 'update']);
    Route::delete('/pelanggan/{id}', [PelangganWebController::class, 'destroy']);

    // Area CRUD
    Route::get('/area', [AreaWebController::class, 'index']);
    Route::post('/area', [AreaWebController::class, 'store']);
    Route::put('/area/{id}', [AreaWebController::class, 'update']);
    Route::delete('/area/{id}', [AreaWebController::class, 'destroy']);

    // Paket CRUD
    Route::get('/paket', [PaketWebController::class, 'index']);
    Route::post('/paket', [PaketWebController::class, 'store']);
    Route::put('/paket/{id}', [PaketWebController::class, 'update']);
    Route::delete('/paket/{id}', [PaketWebController::class, 'destroy']);

    // Other pages that still use old Blade (fallback)
    $legacyPages = [
        'dashboard-teknisi', 'app-teknisi', 'app-pelanggan',
        'tagihan', 'pengaturan', 'buku-kas',
        'messaging-pelanggan', 'messaging-lapangan',
        'pembukuan-kang-tagih', 'pembukuan-saya',
        'pemasangan-baru', 'struk-pembayaran', 'struk',
        'tugas-teknisi', 'troubleshoot', 'tagih-pelanggan',
        'pengumuman',
    ];
    $legacyPattern = implode('|', array_map(static fn (string $s) => preg_quote($s, '/'), $legacyPages));
    Route::get('/{slug}', [BillingPageController::class, 'show'])->where('slug', $legacyPattern);
});

Route::get('/portal-pelanggan', [BillingPageController::class, 'portalPelanggan']);

// Legacy HTML redirects
Route::redirect('/login.html', '/login', 301);
$allPaths = array_merge(
    ['dashboard-admin', 'pelanggan', 'area', 'paket'],
    ['dashboard-teknisi', 'app-teknisi', 'app-pelanggan', 'tagihan', 'pengaturan', 'buku-kas',
     'messaging-pelanggan', 'messaging-lapangan', 'pembukuan-kang-tagih', 'pembukuan-saya',
     'pemasangan-baru', 'struk-pembayaran', 'struk', 'tugas-teknisi', 'troubleshoot',
     'tagih-pelanggan', 'pengumuman', 'portal-pelanggan'],
);
foreach ($allPaths as $path) {
    Route::redirect('/' . $path . '.html', '/' . $path, 301);
}
