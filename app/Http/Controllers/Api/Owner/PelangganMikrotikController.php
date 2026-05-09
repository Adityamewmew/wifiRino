<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Services\BillingCollectionService;
use App\Support\RowSerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PelangganMikrotikController extends Controller
{
    private function resolvePelangganDbId(string $key): ?string
    {
        if (DB::table('pelanggan')->where('id', $key)->exists()) {
            return $key;
        }
        $row = DB::table('pelanggan')->where('idPelanggan', $key)->first();

        return $row->id ?? null;
    }

    public function show(Request $request, string $key)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! BillingCollectionService::canWriteCollection('pelanggan', $jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $pid = $this->resolvePelangganDbId($key);
        if (! $pid) {
            return response()->json(['error' => 'Pelanggan tidak ditemukan'], 404);
        }
        $row = DB::table('pelanggan_mikrotik')->where('pelangganDbId', $pid)->first();

        return response()->json($row ? RowSerializer::deserializeRow((array) $row) : new \stdClass);
    }

    public function update(Request $request, string $key)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! BillingCollectionService::canWriteCollection('pelanggan', $jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $pid = $this->resolvePelangganDbId($key);
        if (! $pid) {
            return response()->json(['error' => 'Pelanggan tidak ditemukan'], 404);
        }
        $p = $request->all();
        $data = [
            'routerId' => $p['routerId'] ?? null,
            'profile' => $p['profile'] ?? null,
            'ipPool' => $p['ipPool'] ?? null,
            'isolirAddressList' => $p['isolirAddressList'] ?? 'isolir-billing',
            'simpleQueueName' => $p['simpleQueueName'] ?? null,
            'catatanTeknis' => $p['catatanTeknis'] ?? null,
        ];
        $data = RowSerializer::serializeForDb($data);
        $exists = DB::table('pelanggan_mikrotik')->where('pelangganDbId', $pid)->exists();
        if ($exists) {
            DB::table('pelanggan_mikrotik')->where('pelangganDbId', $pid)->update($data);
        } else {
            $data['id'] = (string) Str::uuid();
            $data['pelangganDbId'] = $pid;
            DB::table('pelanggan_mikrotik')->insert($data);
        }

        return response()->json(['success' => true]);
    }
}
