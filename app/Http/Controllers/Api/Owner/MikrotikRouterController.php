<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Support\RoleHelper;
use App\Support\RowSerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MikrotikRouterController extends Controller
{
    public function index(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::canManageSettings($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $rows = DB::table('mikrotik_routers')->orderBy('nama')->get();
        $counts = DB::table('pelanggan_mikrotik')
            ->select('routerId', DB::raw('COUNT(*) as c'))
            ->whereNotNull('routerId')
            ->groupBy('routerId')
            ->pluck('c', 'routerId');
        $data = $rows->map(function ($r) use ($counts) {
            $arr = (array) $r;
            $arr['pelangganCount'] = (int) ($counts[$arr['id']] ?? 0);

            return $arr;
        })->values()->all();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::canManageSettings($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $payload = $request->all();
        $id = (string) Str::uuid();
        $data = [
            'id' => $id,
            'nama' => trim((string) ($payload['nama'] ?? '')),
            'host' => $payload['host'] ?? null,
            'apiPort' => (int) ($payload['apiPort'] ?? 8728),
            'apiUser' => $payload['apiUser'] ?? null,
            'apiPassword' => $payload['apiPassword'] ?? null,
            'keterangan' => $payload['keterangan'] ?? null,
            'rosVersi' => $payload['rosVersi'] ?? 'V6',
            'userManager' => $payload['userManager'] ?? null,
            'hotspotManager' => $payload['hotspotManager'] ?? 'tidak_aktif',
            'serviceType' => $payload['serviceType'] ?? 'API',
        ];
        if ($data['nama'] === '') {
            return response()->json(['error' => 'Nama router wajib diisi'], 400);
        }
        DB::table('mikrotik_routers')->insert(RowSerializer::serializeForDb($data));

        return response()->json(['success' => true, 'id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::canManageSettings($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        if (! DB::table('mikrotik_routers')->where('id', $id)->exists()) {
            return response()->json(['error' => 'Tidak ditemukan'], 404);
        }
        $payload = $request->all();
        $data = [];
        foreach (['nama', 'host', 'apiPort', 'apiUser', 'keterangan', 'rosVersi', 'userManager', 'hotspotManager', 'serviceType'] as $k) {
            if (array_key_exists($k, $payload)) {
                $data[$k] = $payload[$k];
            }
        }
        if (array_key_exists('apiPassword', $payload) && $payload['apiPassword'] !== null && $payload['apiPassword'] !== '') {
            $data['apiPassword'] = $payload['apiPassword'];
        }
        if (isset($data['nama']) && trim((string) $data['nama']) === '') {
            return response()->json(['error' => 'Nama router tidak boleh kosong'], 400);
        }
        if ($data === []) {
            return response()->json(['success' => true]);
        }
        DB::table('mikrotik_routers')->where('id', $id)->update(RowSerializer::serializeForDb($data));

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::canManageSettings($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        DB::table('pelanggan_mikrotik')->where('routerId', $id)->update(['routerId' => null]);
        DB::table('mikrotik_routers')->where('id', $id)->delete();

        return response()->json(['success' => true]);
    }

    public function probe(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::canManageSettings($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $row = DB::table('mikrotik_routers')->where('id', $id)->first();
        if (! $row) {
            return response()->json(['ok' => false, 'message' => 'Router tidak ditemukan'], 404);
        }
        $host = trim((string) ($row->host ?? ''));
        $port = (int) ($row->apiPort ?? 8728);
        if ($host === '') {
            return response()->json(['ok' => false, 'message' => 'Host kosong'], 200);
        }
        $t0 = microtime(true);
        $errno = 0;
        $errstr = '';
        $fp = @fsockopen($host, $port, $errno, $errstr, 4.0);
        $ms = (microtime(true) - $t0) * 1000;
        if ($fp) {
            fclose($fp);
            DB::table('mikrotik_routers')->where('id', $id)->update([
                'lastProbeAt' => now()->format('Y-m-d H:i:s'),
                'lastProbeMs' => (int) round($ms),
                'lastProbeOk' => 1,
            ]);

            return response()->json(['ok' => true, 'latencyMs' => round($ms, 2), 'message' => 'TCP OK']);
        }
        DB::table('mikrotik_routers')->where('id', $id)->update([
            'lastProbeAt' => now()->format('Y-m-d H:i:s'),
            'lastProbeMs' => null,
            'lastProbeOk' => 0,
        ]);

        return response()->json(['ok' => false, 'message' => $errstr ?: 'Koneksi gagal', 'errno' => $errno]);
    }
}
