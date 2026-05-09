<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembukuanAgenSummaryController extends Controller
{
    public function index(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        $bulan = max(1, min(12, (int) $request->query('bulan', (int) date('n'))));
        $tahun = max(2000, (int) $request->query('tahun', (int) date('Y')));

        $uid = $jwt['uid'] ?? null;
        $user = $uid ? DB::table('users')->where('id', $uid)->first() : null;
        $nama = trim((string) ($user->nama ?? ''));
        $email = trim((string) ($jwt['email'] ?? ''));

        $q = DB::table('tagihan_bulanan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereRaw("LOWER(COALESCE(status,'')) = 'lunas'");
        if ($nama !== '' || $email !== '') {
            $q->where(function ($w) use ($nama, $email) {
                if ($nama !== '' && $email !== '') {
                    $w->where('dibayar_ke', 'like', '%'.$nama.'%')->orWhere('dibayar_ke', $email);
                } elseif ($nama !== '') {
                    $w->where('dibayar_ke', 'like', '%'.$nama.'%');
                } else {
                    $w->where('dibayar_ke', $email);
                }
            });
        }
        $tags = $q->get();
        $totalTagihan = (float) $tags->sum('totalTagihan');
        $jumlahPelanggan = $tags->pluck('idPelanggan')->unique()->count();

        $setor = (float) DB::table('pembukuan')
            ->where('kategori', 'Setoran Tunai Agen')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->when($nama !== '', fn ($w) => $w->where('keterangan', 'like', '%'.$nama.'%'))
            ->sum('nominal');

        $row = [
            'namaPenagih' => $nama,
            'totalTagihan' => $totalTagihan,
            'jumlahPelanggan' => $jumlahPelanggan,
            'setor' => $setor,
            'sisaDiTangan' => max(0, $totalTagihan - $setor),
        ];

        if ($tags->isEmpty() && $setor <= 0) {
            return response()->json(['success' => true, 'data' => []]);
        }

        return response()->json(['success' => true, 'data' => [$row]]);
    }
}
