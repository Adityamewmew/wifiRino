<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BillingPageController extends Controller
{
    /**
     * Slug → view mapping sesuai struktur baru:
     *   billing/admin/*   — halaman admin/owner
     *   billing/teknisi/* — halaman tim lapangan
     *   billing/pelanggan/* — halaman portal pelanggan
     */
    private static array $viewMap = [
        // ── Admin ────────────────────────────────────────
        'tagihan'              => 'billing.admin.tagihan.index',
        'tagih-pelanggan'      => 'billing.admin.tagihan.detail',
        'buku-kas'             => 'billing.admin.buku_kas',
        'pengaturan'           => 'billing.admin.pengaturan',
        'pengumuman'           => 'billing.admin.pengumuman',
        'pembukuan-kang-tagih' => 'billing.admin.pembukuan.kang_tagih',
        'pembukuan-saya'       => 'billing.admin.pembukuan.saya',
        'messaging-pelanggan'  => 'billing.admin.pelanggan.messaging',

        // ── Teknisi / Lapangan ───────────────────────────
        'dashboard-teknisi'    => 'billing.teknisi.dashboard',
        'app-teknisi'          => 'billing.teknisi.app',
        'tugas-teknisi'        => 'billing.teknisi.tugas.index',
        'pemasangan-baru'      => 'billing.teknisi.pemasangan_baru',
        'troubleshoot'         => 'billing.teknisi.troubleshoot',
        'messaging-lapangan'   => 'billing.teknisi.messaging',

        // ── Pelanggan ────────────────────────────────────
        'portal-pelanggan'     => 'billing.pelanggan.portal',
        'app-pelanggan'        => 'billing.pelanggan.app',
        'struk'                => 'billing.pelanggan.struk',
        'struk-pembayaran'     => 'billing.pelanggan.struk_pembayaran',
    ];

    public function show(string $slug): View
    {
        return $this->renderPage($slug);
    }

    public function portalPelanggan(): View
    {
        return $this->renderPage('portal-pelanggan');
    }

    private function renderPage(string $slug): View
    {
        $view = self::$viewMap[$slug] ?? null;

        if (! $view || ! view()->exists($view)) {
            abort(404);
        }

        return view($view);
    }
}
