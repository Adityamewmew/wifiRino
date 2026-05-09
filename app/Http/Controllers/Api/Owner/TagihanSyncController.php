<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Support\PelangganBillingHelper;
use App\Support\RowSerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagihanSyncController extends Controller
{
    public function sync(Request $request)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');
        if ($bulan === null || $tahun === null || $bulan === '' || $tahun === '') {
            return response()->json(['error' => 'Bulan dan tahun diperlukan'], 400);
        }
        $blnInt = (int) $bulan;
        $thnInt = (int) $tahun;
        if ($blnInt < 1 || $blnInt > 12 || $thnInt < 2000) {
            return response()->json(['error' => 'Parameter bulan/tahun tidak valid'], 400);
        }
        $selectedDate = mktime(0, 0, 0, $blnInt, 1, $thnInt);

        $pelangganArr = DB::table('pelanggan')->where('status', 'aktif')->get();
        $existingRows = DB::table('tagihan_bulanan')
            ->where('bulan', $blnInt)
            ->where('tahun', $thnInt)
            ->pluck('idPelanggan');
        $existingSet = collect($existingRows)->map(fn ($v) => trim((string) $v))->filter()->flip()->all();

        $countBaru = 0;

        foreach ($pelangganArr as $row) {
            $pData = RowSerializer::deserializeRow((array) $row) ?? [];

            $mulaiDate = null;
            if (! empty($pData['mulaiTagihan'])) {
                $t = strtotime((string) $pData['mulaiTagihan']);
                if ($t !== false) {
                    $mulaiDate = mktime(0, 0, 0, (int) date('n', $t), 1, (int) date('Y', $t));
                }
            }
            if ($mulaiDate === null && ! empty($pData['tanggalMulaiStr'])) {
                $t = strtotime((string) $pData['tanggalMulaiStr']);
                if ($t !== false) {
                    $mulaiDate = mktime(0, 0, 0, (int) date('n', $t) + 1, 1, (int) date('Y', $t));
                }
            }
            if ($mulaiDate === null && isset($pData['bulanMulai'], $pData['tahunMulai'])) {
                $mulaiDate = mktime(0, 0, 0, (int) $pData['bulanMulai'], 1, (int) $pData['tahunMulai']);
            }
            if ($mulaiDate !== null && $mulaiDate > $selectedDate) {
                continue;
            }

            $pelId = trim((string) ($pData['idPelanggan'] ?? $pData['id'] ?? ''));
            if ($pelId === '') {
                continue;
            }
            if (isset($existingSet[$pelId])) {
                continue;
            }

            $defaultTglTagih = isset($pData['tglTagih']) ? (int) $pData['tglTagih'] : 10;
            $maxDay = (int) date('t', mktime(0, 0, 0, $blnInt, 1, $thnInt));
            $day = min(max($defaultTglTagih, 1), $maxDay);
            $jatuhTempo = date('Y-m-d H:i:s', mktime(23, 59, 59, $blnInt, $day, $thnInt));

            $biaya = PelangganBillingHelper::inferBiayaTagihanDariPelanggan($pData);
            $newId = (string) Str::uuid();
            $newData = [
                'id' => $newId,
                'idPelanggan' => $pelId,
                'namaPelanggan' => $pData['nama'] ?? 'Pelanggan Tanpa Nama',
                'area' => $pData['area'] ?? 'Unknown',
                'paket' => $pData['paket'] ?? '-',
                'noWA' => $pData['noWA'] ?? '',
                'bulan' => $blnInt,
                'tahun' => $thnInt,
                'totalTagihan' => $biaya,
                'status' => 'belum',
                'tglJatuhTempo' => $jatuhTempo,
                'diskonSnapshot' => PelangganBillingHelper::diskonSnapshotFromPelanggan($pData),
                'biayaSnapshot' => PelangganBillingHelper::biayaSnapshotFromPelanggan($pData),
                'createdAt' => now()->toIso8601String(),
            ];
            $serialized = RowSerializer::serializeForDb($newData);
            DB::table('tagihan_bulanan')->insert($serialized);
            $countBaru++;
            $existingSet[$pelId] = true;
        }

        return response()->json(['success' => true, 'generated' => $countBaru]);
    }
}
