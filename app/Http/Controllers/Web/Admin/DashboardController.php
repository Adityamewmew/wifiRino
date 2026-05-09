<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Support\RoleHelper;
use App\Support\RowSerializer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = session('staff_user', []);

        $bulan = max(1, min(12, (int) $request->query('bulan', (int) date('n'))));
        $tahun = max(2000, min(2100, (int) $request->query('tahun', (int) date('Y'))));
        $canViewFinance = in_array('view_finance_totals', $user['permissions'] ?? []);

        $start = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $end = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // Statistik pelanggan
        $totalPelanggan = (int) DB::table('pelanggan')->count();
        $pelangganAktif = (int) DB::table('pelanggan')->where('status', 'aktif')->count();
        $pelangganBaru = (int) DB::table('pelanggan')
            ->whereBetween('createdAt', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->count();
        $pelangganBerhenti = (int) DB::table('pelanggan')
            ->where('status', 'berhenti')
            ->whereBetween('updatedAt', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->count();

        // Statistik tagihan
        $tagihanRows = DB::table('tagihan_bulanan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        $jmlLunas = 0;
        $jmlBelum = 0;
        $nominalLunas = 0.0;
        $nominalBelum = 0.0;
        $rowsBelum = [];

        foreach ($tagihanRows as $row) {
            $t = RowSerializer::deserializeRow((array) $row) ?? [];
            $nominal = (float) ($t['totalTagihan'] ?? $t['nominal'] ?? 0);
            $st = (string) ($t['status'] ?? '');
            if ($st === 'lunas') {
                $jmlLunas++;
                $nominalLunas += $nominal;
            } else {
                $jmlBelum++;
                $nominalBelum += $nominal;
                $rowsBelum[] = [
                    'idPelanggan' => $t['idPelanggan'] ?? null,
                    'namaPelanggan' => $t['namaPelanggan'] ?? null,
                    'area' => $t['area'] ?? null,
                    'totalTagihan' => $nominal,
                ];
            }
        }

        usort($rowsBelum, fn ($a, $b) => ($b['totalTagihan'] ?? 0) <=> ($a['totalTagihan'] ?? 0));

        $totalTagihan = $tagihanRows->count();
        $persenLunas = $totalTagihan > 0 ? (int) round(($jmlLunas / $totalTagihan) * 100) : 0;
        $tunggakanPreview = array_slice($rowsBelum, 0, 5);

        // Revenue trend 12 bulan
        $trendLabels = [];
        $trendData = [];
        if ($canViewFinance) {
            for ($i = 11; $i >= 0; $i--) {
                $t = strtotime("-{$i} months");
                $y = (int) date('Y', $t);
                $m = (int) date('n', $t);
                $trendLabels[] = date('M Y', $t);
                $sum = (float) DB::table('pembukuan')
                    ->where('jenis', 'pemasukan')
                    ->whereYear('tanggal', $y)
                    ->whereMonth('tanggal', $m)
                    ->sum('nominal');
                $trendData[] = $sum;
            }
        }

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $activeMenu = 'dashboard';

        return view('admin.dashboard', compact(
            'activeMenu',
            'bulan', 'tahun', 'namaBulan', 'canViewFinance',
            'totalPelanggan', 'pelangganAktif', 'pelangganBaru', 'pelangganBerhenti',
            'jmlLunas', 'jmlBelum', 'nominalLunas', 'nominalBelum',
            'totalTagihan', 'persenLunas', 'tunggakanPreview',
            'trendLabels', 'trendData'
        ));
    }
}
