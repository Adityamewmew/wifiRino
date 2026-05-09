<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Services\BillingCollectionService;
use App\Support\RoleHelper;
use App\Support\RowSerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelangganMasterController extends Controller
{
    /**
     * Daftar pelanggan untuk halaman master (filter & pencarian di server — logika dari pelanggan.js).
     */
    public function index(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::hasPermission($jwt, 'access_admin_app')) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $area = (string) $request->query('area', 'all');
        $status = (string) $request->query('status', 'all');
        $paket = (string) $request->query('paket', 'all');
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('pelanggan');

        if ($area !== '' && $area !== 'all') {
            $query->where('area', $area);
        }

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        } else {
            $query->where(function ($w): void {
                $w->whereNull('status')->orWhere('status', '<>', 'berhenti');
            });
        }

        if ($paket !== '' && $paket !== 'all') {
            $query->where('paket', $paket);
        }

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where(function ($w) use ($like): void {
                $w->where('nama', 'like', $like)
                    ->orWhere('idPelanggan', 'like', $like)
                    ->orWhere('idPPOE', 'like', $like)
                    ->orWhere('noWA', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('noKtp', 'like', $like)
                    ->orWhere('keterangan', 'like', $like);
            });
        }

        $rows = $query->orderBy('nama')->get();
        $results = $rows->map(fn ($row) => RowSerializer::deserializeRow((array) $row))->all();
        $results = BillingCollectionService::injectBayarDimuka($results, 'pelanggan');

        return response()->json($results);
    }
}
