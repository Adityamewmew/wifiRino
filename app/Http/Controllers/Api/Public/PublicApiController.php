<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Support\RowSerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicApiController extends Controller
{
    public function clientLogin(Request $request)
    {
        $pelId = $request->input('pelId');
        if (! $pelId) {
            return response()->json(['error' => 'ID atau No WA diperlukan'], 400);
        }
        $inputId = strtolower(trim((string) $pelId));
        $inputWa = preg_replace('/\D/', '', $inputId);
        if (str_starts_with($inputWa, '0')) {
            $inputWa = '62'.substr($inputWa, 1);
        }

        $rows = DB::table('pelanggan')->get();
        $matchRow = null;
        foreach ($rows as $row) {
            $r = RowSerializer::deserializeRow((array) $row) ?? [];
            $id = strtolower(trim((string) ($r['idPelanggan'] ?? $r['id'] ?? '')));
            $pWa = preg_replace('/\D/', '', (string) ($r['noWA'] ?? ''));
            if (str_starts_with($pWa, '0')) {
                $pWa = '62'.substr($pWa, 1);
            }
            if ($id === $inputId || (strlen($inputWa) >= 10 && $pWa === $inputWa)) {
                $matchRow = $r;
                break;
            }
        }
        if ($matchRow === null) {
            return response()->json(['error' => 'ID Pelanggan atau Nomor WA tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $matchRow['id'] ?? null,
                'idPelanggan' => $matchRow['idPelanggan'] ?? $matchRow['id'] ?? null,
                'nama' => $matchRow['nama'] ?? 'Pelanggan',
                'area' => $matchRow['area'] ?? null,
                'paket' => $matchRow['paket'] ?? null,
                'noWA' => $matchRow['noWA'] ?? null,
                'status' => $matchRow['status'] ?? 'aktif',
            ],
        ]);
    }

    public function tagihanPelanggan(Request $request, string $idPelanggan)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');
        $q = DB::table('tagihan_bulanan')->where('idPelanggan', $idPelanggan);
        if ($bulan !== null && $bulan !== '') {
            $q->where('bulan', (int) $bulan);
        }
        if ($tahun !== null && $tahun !== '') {
            $q->where('tahun', (int) $tahun);
        }
        $rows = $q->orderByDesc('tahun')->orderByDesc('bulan')->limit(12)->get();
        $data = $rows->map(fn ($row) => RowSerializer::deserializeRow((array) $row))->all();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function paketPublik()
    {
        $rows = DB::table('paket')
            ->select('id', 'nama', 'harga', 'deskripsi', 'aktif')
            ->whereRaw('COALESCE(aktif,1) = 1')
            ->orderBy('harga')
            ->orderBy('nama')
            ->get();
        $data = $rows->map(fn ($row) => RowSerializer::deserializeRow((array) $row))->all();

        return response()->json(['success' => true, 'data' => $data]);
    }
}
