<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Support\PelangganBillingHelper;
use App\Support\RoleHelper;
use App\Support\RowSerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BayarDimukaController extends Controller
{
    private function canDimuka(array $jwt): bool
    {
        return RoleHelper::hasPermission($jwt, 'access_admin_app')
            || RoleHelper::hasPermission($jwt, 'collect_customer_payment')
            || RoleHelper::isAdminKeuanganOrOwner($jwt);
    }

    private function latestBatchId(string $idPelanggan): ?string
    {
        $row = DB::table('tagihan_bulanan')
            ->where('idPelanggan', $idPelanggan)
            ->whereNotNull('dimukaBatchId')
            ->select('dimukaBatchId', DB::raw('MAX(tglBayar) as mx'))
            ->groupBy('dimukaBatchId')
            ->orderByDesc('mx')
            ->first();

        return $row->dimukaBatchId ?? null;
    }

    public function latest(Request $request, string $idPelanggan)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! $this->canDimuka($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $batchId = $this->latestBatchId($idPelanggan);
        if (! $batchId) {
            return response()->json(['exists' => false]);
        }
        $rows = DB::table('tagihan_bulanan')
            ->where('idPelanggan', $idPelanggan)
            ->where('dimukaBatchId', $batchId)
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();
        $bulan = [];
        foreach ($rows as $r) {
            $bulan[] = (int) $r->bulan.'/'.(int) $r->tahun;
        }

        return response()->json([
            'exists' => true,
            'data' => [
                'jumlahBulan' => count($bulan),
                'bulan' => $bulan,
                'batchId' => $batchId,
            ],
        ]);
    }

    private function ensureTagihanRow(string $idPelanggan, int $bulan, int $tahun): object
    {
        $row = DB::table('tagihan_bulanan')
            ->where('idPelanggan', $idPelanggan)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();
        if ($row) {
            return $row;
        }
        $pRow = DB::table('pelanggan')->where('idPelanggan', $idPelanggan)->first();
        if (! $pRow) {
            abort(404, 'Pelanggan tidak ditemukan');
        }
        $pData = RowSerializer::deserializeRow((array) $pRow) ?? [];
        $biaya = PelangganBillingHelper::inferBiayaTagihanDariPelanggan($pData);
        $defaultTglTagih = isset($pData['tglTagih']) ? (int) $pData['tglTagih'] : 10;
        $maxDay = (int) date('t', mktime(0, 0, 0, $bulan, 1, $tahun));
        $day = min(max($defaultTglTagih, 1), $maxDay);
        $jatuhTempo = date('Y-m-d H:i:s', mktime(23, 59, 59, $bulan, $day, $tahun));
        $newId = (string) Str::uuid();
        $newData = [
            'id' => $newId,
            'idPelanggan' => $idPelanggan,
            'namaPelanggan' => $pData['nama'] ?? 'Pelanggan Tanpa Nama',
            'area' => $pData['area'] ?? 'Unknown',
            'paket' => $pData['paket'] ?? '-',
            'noWA' => $pData['noWA'] ?? '',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'totalTagihan' => $biaya,
            'status' => 'belum',
            'tglJatuhTempo' => $jatuhTempo,
            'diskonSnapshot' => PelangganBillingHelper::diskonSnapshotFromPelanggan($pData),
            'biayaSnapshot' => PelangganBillingHelper::biayaSnapshotFromPelanggan($pData),
            'createdAt' => now()->format('Y-m-d H:i:s'),
        ];
        DB::table('tagihan_bulanan')->insert(RowSerializer::serializeForDb($newData));

        return DB::table('tagihan_bulanan')->where('id', $newId)->first();
    }

    private function applyBatch(string $idPelanggan, array $periodeList, string $keterangan, ?string $forcedBatchId = null): string
    {
        $batchId = $forcedBatchId ?? (string) Str::uuid();
        $total = 0.0;
        $nApplied = 0;
        DB::transaction(function () use ($idPelanggan, $periodeList, $keterangan, $batchId, &$total, &$nApplied) {
            foreach ($periodeList as $per) {
                $bulan = (int) ($per['bulan'] ?? 0);
                $tahun = (int) ($per['tahun'] ?? 0);
                if ($bulan < 1 || $bulan > 12 || $tahun < 2000) {
                    continue;
                }
                $tag = $this->ensureTagihanRow($idPelanggan, $bulan, $tahun);
                $st = strtolower((string) $tag->status);
                $dbb = $tag->dimukaBatchId ?? null;
                if ($st === 'lunas' && ($dbb === null || $dbb === '')) {
                    continue;
                }
                if ($st === 'lunas' && $dbb !== $batchId && $dbb !== null && $dbb !== '') {
                    continue;
                }
                $total += (float) $tag->totalTagihan;
                $nApplied++;
                DB::table('tagihan_bulanan')->where('id', $tag->id)->update(RowSerializer::serializeForDb([
                    'status' => 'lunas',
                    'tglBayar' => now()->format('Y-m-d H:i:s'),
                    'metodeBayar' => 'Bayar Dimuka',
                    'dimukaBatchId' => $batchId,
                ]));
            }
            if ($nApplied === 0) {
                throw new \RuntimeException('Tidak ada tagihan yang dapat diproses untuk bayar dimuka.');
            }
            DB::table('pembukuan')->insert(RowSerializer::serializeForDb([
                'id' => (string) Str::uuid(),
                'tanggal' => now()->format('Y-m-d H:i:s'),
                'jenis' => 'pemasukan',
                'kategori' => 'Bayar Dimuka',
                'nominal' => $total,
                'keterangan' => $keterangan !== '' ? $keterangan : ('Bayar dimuka '.$idPelanggan),
                'idReferensi' => $batchId,
                'createdBy' => null,
            ]));
        });

        return $batchId;
    }

    public function store(Request $request, string $idPelanggan)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! $this->canDimuka($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $periodeList = $request->input('periodeList');
        if (! is_array($periodeList) || $periodeList === []) {
            return response()->json(['error' => 'periodeList wajib diisi'], 400);
        }
        $ket = trim((string) $request->input('keterangan', ''));
        try {
            $this->applyBatch($idPelanggan, $periodeList, $ket);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['success' => true]);
    }

    public function updateBatch(Request $request, string $idPelanggan)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! $this->canDimuka($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $periodeList = $request->input('periodeList');
        if (! is_array($periodeList) || $periodeList === []) {
            return response()->json(['error' => 'periodeList wajib diisi'], 400);
        }
        $ket = trim((string) $request->input('keterangan', ''));
        $this->cancelBatchInternal($idPelanggan);
        try {
            $this->applyBatch($idPelanggan, $periodeList, $ket);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['success' => true]);
    }

    private function cancelBatchInternal(string $idPelanggan): void
    {
        $batchId = $this->latestBatchId($idPelanggan);
        if (! $batchId) {
            return;
        }
        DB::transaction(function () use ($idPelanggan, $batchId) {
            DB::table('tagihan_bulanan')
                ->where('idPelanggan', $idPelanggan)
                ->where('dimukaBatchId', $batchId)
                ->update([
                    'status' => 'belum',
                    'tglBayar' => null,
                    'metodeBayar' => null,
                    'dimukaBatchId' => null,
                ]);
            DB::table('pembukuan')->where('idReferensi', $batchId)->where('kategori', 'Bayar Dimuka')->delete();
        });
    }

    public function cancel(Request $request, string $idPelanggan)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! $this->canDimuka($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $this->cancelBatchInternal($idPelanggan);

        return response()->json(['success' => true]);
    }
}
