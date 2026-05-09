<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Support\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function pruneAreas(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::isOwnerUser($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $keep = $request->input('keepAreaNames');
        if (! is_array($keep)) {
            return response()->json(['error' => 'keepAreaNames wajib array'], 400);
        }
        $keepNorm = array_map(fn ($s) => mb_strtolower(trim((string) $s)), $keep);
        $keepNorm = array_values(array_filter($keepNorm, fn ($s) => $s !== ''));
        $deleted = [];
        $skipped = 0;
        $rows = DB::table('areas')->get();
        foreach ($rows as $a) {
            $nama = trim((string) $a->nama);
            if ($nama === '') {
                continue;
            }
            if (in_array(mb_strtolower($nama), $keepNorm, true)) {
                continue;
            }
            $inUse = DB::table('pelanggan')->where('area', $nama)->exists()
                || DB::table('tagihan_bulanan')->where('area', $nama)->exists();
            if ($inUse) {
                $skipped++;

                continue;
            }
            DB::table('areas')->where('id', $a->id)->delete();
            $deleted[] = $nama;
        }

        return response()->json([
            'success' => true,
            'deletedCount' => count($deleted),
            'deleted' => array_slice($deleted, 0, 100),
            'skippedInUseCount' => $skipped,
        ]);
    }

    public function prunePaket(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::isOwnerUser($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $keep = $request->input('keepPaketNames');
        if (! is_array($keep)) {
            return response()->json(['error' => 'keepPaketNames wajib array'], 400);
        }
        $keepNorm = array_map(fn ($s) => mb_strtolower(trim((string) $s)), $keep);
        $keepNorm = array_values(array_filter($keepNorm, fn ($s) => $s !== ''));
        $deleted = [];
        $skipped = 0;
        $rows = DB::table('paket')->get();
        foreach ($rows as $p) {
            $nama = trim((string) $p->nama);
            if ($nama === '') {
                continue;
            }
            if (in_array(mb_strtolower($nama), $keepNorm, true)) {
                continue;
            }
            $inUse = DB::table('pelanggan')->where('paket', $nama)->exists()
                || DB::table('tagihan_bulanan')->where('paket', $nama)->exists();
            if ($inUse) {
                $skipped++;

                continue;
            }
            DB::table('paket')->where('id', $p->id)->delete();
            $deleted[] = $nama;
        }

        return response()->json([
            'success' => true,
            'deletedCount' => count($deleted),
            'deleted' => array_slice($deleted, 0, 100),
            'skippedInUseCount' => $skipped,
        ]);
    }
}
