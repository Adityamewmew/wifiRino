<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Support\RoleHelper;
use App\Support\RowSerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagihanActionController extends Controller
{
    public function bayar(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::hasPermission($jwt, 'collect_customer_payment') && ! RoleHelper::isAdminKeuanganOrOwner($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $tag = DB::table('tagihan_bulanan')->where('id', $id)->first();
        if (! $tag) {
            return response()->json(['error' => 'Tagihan tidak ditemukan'], 404);
        }
        if (strtolower((string) $tag->status) === 'lunas') {
            return response()->json(['error' => 'Tagihan sudah lunas'], 409);
        }
        DB::transaction(function () use ($tag, $id, $jwt) {
            DB::table('tagihan_bulanan')->where('id', $id)->update(RowSerializer::serializeForDb([
                'status' => 'lunas',
                'tglBayar' => now()->format('Y-m-d H:i:s'),
                'metodeBayar' => 'Manual',
                'dibayar_ke' => $jwt['email'] ?? null,
            ]));
            DB::table('pembukuan')->insert(RowSerializer::serializeForDb([
                'id' => (string) Str::uuid(),
                'tanggal' => now()->format('Y-m-d H:i:s'),
                'jenis' => 'pemasukan',
                'kategori' => 'Tagihan Internet',
                'nominal' => $tag->totalTagihan,
                'keterangan' => 'Bayar tagihan '.$tag->idPelanggan.' '.$tag->bulan.'/'.$tag->tahun,
                'idReferensi' => $id,
                'createdBy' => $jwt['uid'] ?? null,
            ]));
        });

        return response()->json(['success' => true]);
    }

    public function undo(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::hasPermission($jwt, 'collect_customer_payment') && ! RoleHelper::isAdminKeuanganOrOwner($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $tag = DB::table('tagihan_bulanan')->where('id', $id)->first();
        if (! $tag) {
            return response()->json(['error' => 'Tagihan tidak ditemukan'], 404);
        }
        if (strtolower((string) $tag->status) !== 'lunas') {
            return response()->json(['error' => 'Tagihan tidak lunas'], 409);
        }

        if (! RoleHelper::isOwnerUser($jwt)) {
            DB::table('pending_delete_requests')->insert([
                'id' => (string) Str::uuid(),
                'requestType' => 'tagihan_undo',
                'targetId' => $id,
                'targetLabel' => ($tag->namaPelanggan ?? '').' — '.$tag->bulan.'/'.$tag->tahun,
                'reason' => 'Batalkan lunas',
                'status' => 'pending',
                'requestedByUid' => $jwt['uid'] ?? null,
                'requestedByEmail' => $jwt['email'] ?? null,
                'requestedByRole' => $jwt['role'] ?? '',
            ]);

            return response()->json(['success' => true, 'mode' => 'requested']);
        }

        DB::transaction(function () use ($id) {
            DB::table('tagihan_bulanan')->where('id', $id)->update(RowSerializer::serializeForDb([
                'status' => 'belum',
                'tglBayar' => null,
                'metodeBayar' => null,
                'dibayar_ke' => null,
            ]));
            DB::table('pembukuan')->where('idReferensi', $id)->where('kategori', 'Tagihan Internet')->delete();
        });

        return response()->json(['success' => true, 'mode' => 'immediate']);
    }

    public function setTglIsolir(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (RoleHelper::resolveRoleKey($jwt['roleKey'] ?? $jwt['role'] ?? '') === 'penagih'
            && ! RoleHelper::isAdminKeuanganOrOwner($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $tag = DB::table('tagihan_bulanan')->where('id', $id)->first();
        if (! $tag) {
            return response()->json(['error' => 'Tagihan tidak ditemukan'], 404);
        }
        $raw = $request->input('tglIsolir');
        $val = null;
        if ($raw !== null && $raw !== '') {
            $val = RowSerializer::coerceMysqlDateTime((string) $raw);
            if (! preg_match('/^\d{4}-\d{2}-\d{2}/', (string) $val)) {
                $val = date('Y-m-d H:i:s', strtotime((string) $raw));
            }
        }
        DB::table('tagihan_bulanan')->where('id', $id)->update(['tglIsolir' => $val]);

        return response()->json(['success' => true]);
    }

    public function pendingDeleteRequests(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::isOwnerUser($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $rows = DB::table('pending_delete_requests')
            ->where('status', 'pending')
            ->orderByDesc('createdAt')
            ->get()
            ->map(fn ($r) => RowSerializer::deserializeRow((array) $r))
            ->all();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function approvePendingDelete(Request $request, string $requestId)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::isOwnerUser($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $req = DB::table('pending_delete_requests')->where('id', $requestId)->where('status', 'pending')->first();
        if (! $req) {
            return response()->json(['error' => 'Request tidak ditemukan'], 404);
        }
        $targetId = (string) $req->targetId;
        if ($req->requestType === 'tagihan_undo') {
            DB::transaction(function () use ($targetId, $requestId, $jwt) {
                DB::table('tagihan_bulanan')->where('id', $targetId)->update(RowSerializer::serializeForDb([
                    'status' => 'belum',
                    'tglBayar' => null,
                    'metodeBayar' => null,
                    'dibayar_ke' => null,
                ]));
                DB::table('pembukuan')->where('idReferensi', $targetId)->where('kategori', 'Tagihan Internet')->delete();
                DB::table('pending_delete_requests')->where('id', $requestId)->update([
                    'status' => 'approved',
                    'approvedByUid' => $jwt['uid'] ?? null,
                    'approvedByEmail' => $jwt['email'] ?? null,
                    'approvedAt' => now()->format('Y-m-d H:i:s'),
                ]);
            });
        } else {
            DB::table('pending_delete_requests')->where('id', $requestId)->update([
                'status' => 'approved',
                'approvedByUid' => $jwt['uid'] ?? null,
                'approvedByEmail' => $jwt['email'] ?? null,
                'approvedAt' => now()->format('Y-m-d H:i:s'),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
