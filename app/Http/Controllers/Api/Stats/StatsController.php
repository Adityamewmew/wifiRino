<?php

namespace App\Http\Controllers\Api\Stats;

use App\Http\Controllers\Controller;
use App\Support\RoleHelper;
use App\Support\RowSerializer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * Ringkasan dashboard admin: agregat pelanggan + tagihan + preview tunggakan (logika dipindah dari dashboard.js).
     */
    public function dashboardSummary(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::hasPermission($jwt, 'access_admin_app')) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $bulan = max(1, min(12, (int) $request->query('bulan', (int) date('n'))));
        $tahun = max(2000, min(2100, (int) $request->query('tahun', (int) date('Y'))));
        $canViewFinance = RoleHelper::canViewFinanceTotals($jwt);

        $start = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $end = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $totalPelanggan = (int) DB::table('pelanggan')->count();
        $pelangganAktif = (int) DB::table('pelanggan')->where('status', 'aktif')->count();
        $pelangganBaru = (int) DB::table('pelanggan')
            ->whereBetween('createdAt', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->count();
        $pelangganBerhenti = (int) DB::table('pelanggan')
            ->where('status', 'berhenti')
            ->whereBetween('updatedAt', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->count();

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
                    '_sortNominal' => $nominal,
                ];
            }
        }

        usort($rowsBelum, function (array $a, array $b): int {
            $na = (float) ($a['_sortNominal'] ?? 0);
            $nb = (float) ($b['_sortNominal'] ?? 0);
            $cmp = $nb <=> $na;
            if ($cmp !== 0) {
                return $cmp;
            }

            return strcasecmp((string) ($a['namaPelanggan'] ?? ''), (string) ($b['namaPelanggan'] ?? ''));
        });

        $previewLimit = 5;
        $tunggakanPreview = array_map(static function (array $r) use ($canViewFinance): array {
            $nom = (float) ($r['_sortNominal'] ?? 0);

            return [
                'idPelanggan' => $r['idPelanggan'],
                'namaPelanggan' => $r['namaPelanggan'],
                'area' => $r['area'],
                'totalTagihan' => $canViewFinance ? $nom : null,
            ];
        }, array_slice($rowsBelum, 0, $previewLimit));
        $totalTagihan = $tagihanRows->count();
        $persenLunas = $totalTagihan > 0 ? (int) round(($jmlLunas / $totalTagihan) * 100) : 0;

        return response()->json([
            'success' => true,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'canViewFinanceTotals' => $canViewFinance,
            'pelanggan' => [
                'total' => $totalPelanggan,
                'aktif' => $pelangganAktif,
                'baruBulanIni' => $pelangganBaru,
                'berhentiBulanIni' => $pelangganBerhenti,
            ],
            'tagihan' => [
                'total' => $totalTagihan,
                'lunas' => $jmlLunas,
                'belum' => $jmlBelum,
                'nominalLunas' => $canViewFinance ? $nominalLunas : null,
                'nominalBelum' => $canViewFinance ? $nominalBelum : null,
                'persenLunas' => $persenLunas,
            ],
            'tunggakanPreview' => $tunggakanPreview,
            'tunggakanBelumCount' => $jmlBelum,
        ]);
    }

    public function revenueTrend(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::canViewFinanceTotals($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $limit = max(1, min(24, (int) $request->query('limit', 12)));
        $labels = [];
        $data = [];
        for ($i = $limit - 1; $i >= 0; $i--) {
            $t = strtotime('-'.$i.' months');
            $y = (int) date('Y', $t);
            $m = (int) date('n', $t);
            $labels[] = date('M Y', $t);
            $sum = (float) DB::table('pembukuan')
                ->where('jenis', 'pemasukan')
                ->whereYear('tanggal', $y)
                ->whereMonth('tanggal', $m)
                ->sum('nominal');
            $data[] = $sum;
        }

        return response()->json(['success' => true, 'labels' => $labels, 'data' => $data]);
    }

    public function performaKaryawan(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::hasPermission($jwt, 'manage_users') && ! RoleHelper::isAdminKeuanganOrOwner($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        return response()->json(['success' => true, 'data' => []]);
    }

    public function keuangan(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::canViewFinanceTotals($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $bulan = max(1, min(12, (int) $request->query('bulan', (int) date('n'))));
        $tahun = max(2000, (int) $request->query('tahun', (int) date('Y')));

        $base = DB::table('pembukuan')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);
        $pemasukan = (clone $base)->where('jenis', 'pemasukan')->sum('nominal');
        $pengeluaran = (clone $base)->where('jenis', 'pengeluaran')->sum('nominal');

        $history = [];
        for ($i = 5; $i >= 0; $i--) {
            $ts = mktime(0, 0, 0, $bulan - $i, 1, $tahun);
            $y = (int) date('Y', $ts);
            $m = (int) date('n', $ts);
            $pm = (float) DB::table('pembukuan')->where('jenis', 'pemasukan')->whereYear('tanggal', $y)->whereMonth('tanggal', $m)->sum('nominal');
            $pk = (float) DB::table('pembukuan')->where('jenis', 'pengeluaran')->whereYear('tanggal', $y)->whereMonth('tanggal', $m)->sum('nominal');
            $history[] = [
                'label' => date('M Y', $ts),
                'pemasukan' => $pm,
                'pengeluaran' => $pk,
            ];
        }

        return response()->json([
            'success' => true,
            'current' => [
                'pemasukan' => (float) $pemasukan,
                'pengeluaran' => (float) $pengeluaran,
                'laba' => (float) $pemasukan - (float) $pengeluaran,
            ],
            'history' => $history,
        ]);
    }
}
