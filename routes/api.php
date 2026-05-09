<?php

use App\Http\Controllers\Api\Auth\AuthApiController;
use App\Http\Controllers\Api\NotImplementedController;
use App\Http\Controllers\Api\Owner\AuditLogController;
use App\Http\Controllers\Api\Owner\BayarDimukaController;
use App\Http\Controllers\Api\Owner\ChatStaffController;
use App\Http\Controllers\Api\Owner\CollectionController;
use App\Http\Controllers\Api\Owner\MaintenanceController;
use App\Http\Controllers\Api\Owner\MikrotikRouterController;
use App\Http\Controllers\Api\Owner\OwnerBulkDeleteController;
use App\Http\Controllers\Api\Owner\PelangganMasterController;
use App\Http\Controllers\Api\Owner\PelangganMikrotikController;
use App\Http\Controllers\Api\Owner\PembukuanAgenSummaryController;
use App\Http\Controllers\Api\Owner\PengaturanController;
use App\Http\Controllers\Api\Owner\TagihanActionController;
use App\Http\Controllers\Api\Owner\TagihanSyncController;
use App\Http\Controllers\Api\Owner\UserDirectoryController;
use App\Http\Controllers\Api\Public\PengumumanPublicController;
use App\Http\Controllers\Api\Public\PublicApiController;
use App\Http\Controllers\Api\Public\PushRegisterController;
use App\Http\Controllers\Api\Stats\StatsController;
use App\Http\Controllers\Api\Payment\TripayController;
use App\Http\Controllers\Api\Teknisi\TugasTeknisiController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthApiController::class, 'login']);
Route::post('auth/client-login', [PublicApiController::class, 'clientLogin']);
Route::get('tagihan/pelanggan/{idPelanggan}', [PublicApiController::class, 'tagihanPelanggan']);
Route::get('paket/publik', [PublicApiController::class, 'paketPublik']);
Route::get('pengaturan', [PengaturanController::class, 'show']);
Route::post('push/register', [PushRegisterController::class, 'register']);
Route::get('pengumuman/aktif', [PengumumanPublicController::class, 'aktif']);

// Tripay Payment Gateway — Public endpoints
Route::post('payment/callback', [TripayController::class, 'callback']);
Route::get('payment/channels', [TripayController::class, 'channels']);
Route::get('payment/status/{reference}', [TripayController::class, 'status']);
Route::post('payment/create-invoice', [TripayController::class, 'createInvoice']);

use App\Http\Controllers\Api\Public\PublicChatController;

Route::get('chat/publik/pelanggan/thread', [PublicChatController::class, 'thread']);
Route::post('chat/publik/pelanggan/messages', [PublicChatController::class, 'postMessage']);

Route::middleware('jwt.auth')->group(function () {
    Route::get('auth/me', [AuthApiController::class, 'me']);
    Route::post('auth/verify-password', [AuthApiController::class, 'verifyPassword']);

    Route::post('pengaturan', [PengaturanController::class, 'store']);

    Route::get('tagihan/sync', [TagihanSyncController::class, 'sync']);
    Route::get('tagihan/pending-delete-requests', [TagihanActionController::class, 'pendingDeleteRequests']);
    Route::post('tagihan/pending-delete-requests/{requestId}/approve', [TagihanActionController::class, 'approvePendingDelete']);
    Route::post('tagihan/{id}/bayar', [TagihanActionController::class, 'bayar']);
    Route::post('tagihan/{id}/undo', [TagihanActionController::class, 'undo']);
    Route::post('tagihan/{id}/tgl-isolir', [TagihanActionController::class, 'setTglIsolir']);

    Route::get('mikrotik-routers', [MikrotikRouterController::class, 'index']);
    Route::post('mikrotik-routers', [MikrotikRouterController::class, 'store']);
    Route::put('mikrotik-routers/{id}', [MikrotikRouterController::class, 'update']);
    Route::delete('mikrotik-routers/{id}', [MikrotikRouterController::class, 'destroy']);
    Route::post('mikrotik-routers/{id}/probe', [MikrotikRouterController::class, 'probe']);

    Route::get('tugas', [TugasTeknisiController::class, 'index']);
    Route::post('tugas', [TugasTeknisiController::class, 'store']);
    Route::patch('tugas/{id}', [TugasTeknisiController::class, 'update']);
    Route::delete('tugas/{id}', [TugasTeknisiController::class, 'destroy']);

    Route::get('users/task-assignees', [UserDirectoryController::class, 'taskAssignees']);

    Route::get('stats/dashboard-summary', [StatsController::class, 'dashboardSummary']);
    Route::get('stats/revenue-trend', [StatsController::class, 'revenueTrend']);
    Route::get('stats/performa-karyawan', [StatsController::class, 'performaKaryawan']);
    Route::get('stats/keuangan', [StatsController::class, 'keuangan']);

    Route::get('audit', [AuditLogController::class, 'index']);

    Route::post('owner/bulk-delete', [OwnerBulkDeleteController::class, 'store']);

    Route::get('pelanggan/master-list', [PelangganMasterController::class, 'index']);

    Route::get('pelanggan/{idPel}/bayar-dimuka/latest', [BayarDimukaController::class, 'latest']);
    Route::post('pelanggan/{idPel}/bayar-dimuka/edit', [BayarDimukaController::class, 'updateBatch']);
    Route::post('pelanggan/{idPel}/bayar-dimuka/cancel', [BayarDimukaController::class, 'cancel']);
    Route::post('pelanggan/{idPel}/bayar-dimuka', [BayarDimukaController::class, 'store']);
    Route::get('pelanggan/{key}/mikrotik', [PelangganMikrotikController::class, 'show']);
    Route::put('pelanggan/{key}/mikrotik', [PelangganMikrotikController::class, 'update']);

    Route::get('pembukuan/agen-summary', [PembukuanAgenSummaryController::class, 'index']);

    Route::post('maintenance/prune-areas-by-import-list', [MaintenanceController::class, 'pruneAreas']);
    Route::post('maintenance/prune-paket-by-import-list', [MaintenanceController::class, 'prunePaket']);

    Route::get('chat/threads/{id}/messages', [ChatStaffController::class, 'messages']);
    Route::post('chat/threads/{id}/messages', [ChatStaffController::class, 'postMessage']);
    Route::post('chat/threads/{id}/claim', [ChatStaffController::class, 'claim']);
    Route::post('chat/threads/{id}/release', [ChatStaffController::class, 'release']);
    Route::post('chat/threads/{id}/delegate', [ChatStaffController::class, 'delegate']);
    Route::post('chat/threads/{id}/field-public', [ChatStaffController::class, 'fieldPublic']);
    Route::get('chat/threads/{id}', [ChatStaffController::class, 'show']);
    Route::get('chat/threads', [ChatStaffController::class, 'threads']);
    Route::get('chat/office-users', [ChatStaffController::class, 'officeUsers']);

    Route::get('collections/{collection}', [CollectionController::class, 'index']);
    Route::get('collections/{collection}/{id}', [CollectionController::class, 'show']);
    Route::post('collections/{collection}', [CollectionController::class, 'store']);
    Route::put('collections/{collection}/{id}', [CollectionController::class, 'update']);
    Route::delete('collections/{collection}/{id}', [CollectionController::class, 'destroy']);

    Route::any('{catch}', NotImplementedController::class)->where('catch', '.*');
});
