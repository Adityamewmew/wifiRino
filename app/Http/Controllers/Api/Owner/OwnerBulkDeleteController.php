<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Services\BillingCollectionService;
use App\Support\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerBulkDeleteController extends Controller
{
    public function store(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::isOwnerUser($jwt)) {
            return response()->json(['error' => 'Hanya Owner yang dapat melakukan bulk delete.'], 403);
        }

        $collection = (string) $request->input('collection', '');
        $table = BillingCollectionService::getTable($collection);
        if (! $table || ! in_array($table, ['pelanggan', 'areas', 'paket'], true)) {
            return response()->json(['error' => 'Koleksi tidak valid'], 400);
        }

        $deleteAll = filter_var($request->input('deleteAll'), FILTER_VALIDATE_BOOLEAN);
        if ($deleteAll) {
            $password = (string) $request->input('password', '');
            $uid = $jwt['uid'] ?? null;
            $row = $uid ? DB::table('users')->select('password')->where('id', $uid)->first() : null;
            if (! $row || ! password_verify($password, (string) $row->password)) {
                return response()->json(['error' => 'Password tidak valid'], 401);
            }
            if ($table === 'pelanggan') {
                $ids = DB::table('pelanggan')->pluck('id')->all();
                $n = 0;
                foreach ($ids as $id) {
                    BillingCollectionService::deleteCollectionRecord('pelanggan', (string) $id, $jwt);
                    $n++;
                }

                return response()->json(['success' => true, 'deletedCount' => $n]);
            }
            $n = DB::table($table)->delete();

            return response()->json(['success' => true, 'deletedCount' => $n]);
        }

        $ids = $request->input('ids');
        if (! is_array($ids) || $ids === []) {
            return response()->json(['error' => 'ids wajib berupa array'], 400);
        }
        $n = 0;
        foreach ($ids as $id) {
            $id = trim((string) $id);
            if ($id === '') {
                continue;
            }
            if ($table === 'pelanggan') {
                BillingCollectionService::deleteCollectionRecord('pelanggan', $id, $jwt);
            } else {
                DB::table($table)->where('id', $id)->delete();
            }
            $n++;
        }

        return response()->json(['success' => true, 'deletedCount' => $n]);
    }
}
