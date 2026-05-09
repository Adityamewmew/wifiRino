<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Support\JwtHelper;
use App\Support\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PengaturanController extends Controller
{
    public function show(Request $request)
    {
        $rows = DB::table('pengaturan')->get();
        $config = [];
        foreach ($rows as $r) {
            $config[$r->kunci] = $r->nilai;
        }
        $jwt = JwtHelper::tryDecode($request);
        $canSecrets = $jwt && RoleHelper::canViewIntegrationSecrets((array) json_decode(json_encode($jwt), true));
        if (! $canSecrets) {
            unset($config['integration_payment_gateway']);
        }

        return response()->json(['success' => true, 'data' => $config]);
    }

    public function store(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::canManageSettings($jwt)) {
            return response()->json(['error' => 'Akses Ditolak. Hanya admin yang bisa menyimpan pengaturan.'], 403);
        }
        $data = $request->all();
        if (! is_array($data)) {
            return response()->json(['error' => 'Payload tidak valid'], 400);
        }
        foreach ($data as $kunci => $nilai) {
            if ($kunci === 'integration_payment_gateway' && ! RoleHelper::canViewIntegrationSecrets($jwt)) {
                continue;
            }
            $valStr = $nilai === null ? '' : (string) $nilai;
            $exists = DB::table('pengaturan')->where('kunci', $kunci)->exists();
            if ($exists) {
                DB::table('pengaturan')->where('kunci', $kunci)->update(['nilai' => $valStr]);
            } else {
                DB::table('pengaturan')->insert(['kunci' => $kunci, 'nilai' => $valStr]);
            }
        }
        $logId = (string) Str::uuid();
        DB::table('audit_logs')->insert([
            'id' => $logId,
            'tanggal' => now(),
            'userEmail' => $jwt['email'] ?? '',
            'userRole' => $jwt['role'] ?? '',
            'aksi' => 'UPDATE',
            'entitas' => 'Pengaturan Sistem',
            'idData' => 'All Configs',
            'keterangan' => 'Memperbarui Info Pembayaran/System',
        ]);

        return response()->json(['success' => true, 'message' => 'Pengaturan berhasil disimpan']);
    }
}
