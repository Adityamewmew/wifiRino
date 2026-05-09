<?php

namespace App\Http\Controllers\Api\Teknisi;

use App\Http\Controllers\Controller;
use App\Support\RoleHelper;
use App\Support\RowSerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TugasTeknisiController extends Controller
{
    public function index(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::hasPermission($jwt, 'access_admin_app') && ! RoleHelper::hasPermission($jwt, 'manage_tasks')) {
            $rk = RoleHelper::resolveRoleKey($jwt['roleKey'] ?? $jwt['role'] ?? '');
            if (! in_array($rk, ['teknisi', 'penagih', 'tekpen'], true)) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }
        }
        $q = DB::table('tugas_teknisi')->orderByDesc('tglDibuat')->orderByDesc('createdAt');
        $st = $request->query('status');
        if ($st !== null && $st !== '') {
            $q->where('status', $st);
        }
        $rows = $q->get()->map(fn ($r) => RowSerializer::deserializeRow((array) $r))->all();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function store(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::hasPermission($jwt, 'manage_tasks')) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $p = $request->all();
        $assignTo = (string) ($p['assignTo'] ?? '');
        $isBroadcast = $assignTo === '__all__' || $assignTo === '' ? 1 : 0;
        if ($assignTo === '') {
            $assignTo = '__all__';
        }
        $judul = trim((string) ($p['judul'] ?? ''));
        if ($judul === '') {
            return response()->json(['error' => 'Judul wajib diisi'], 400);
        }
        $id = (string) Str::uuid();
        $deadline = $p['tglDeadline'] ?? null;
        $data = [
            'id' => $id,
            'judul' => $judul,
            'deskripsi' => $p['deskripsi'] ?? null,
            'jenisTask' => $p['jenisTask'] ?? 'pemasangan',
            'prioritas' => $p['prioritas'] ?? 'normal',
            'status' => 'pending',
            'assignTo' => $isBroadcast ? '__all__' : $assignTo,
            'assignToNama' => $p['assignToNama'] ?? null,
            'isBroadcast' => $isBroadcast,
            'namaPelanggan' => $p['namaPelanggan'] ?? null,
            'noWA' => $p['noWA'] ?? null,
            'alamat' => $p['alamat'] ?? null,
            'tglDibuat' => now()->format('Y-m-d H:i:s'),
            'tglDeadline' => $deadline ? RowSerializer::coerceMysqlDateTime((string) $deadline) : null,
            'createdBy' => $jwt['uid'] ?? null,
        ];
        DB::table('tugas_teknisi')->insert(RowSerializer::serializeForDb($data));

        return response()->json(['success' => true, 'id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        $row = DB::table('tugas_teknisi')->where('id', $id)->first();
        if (! $row) {
            return response()->json(['error' => 'Tugas tidak ditemukan'], 404);
        }
        $p = $request->all();
        $allowed = ['status', 'catatanTeknisi', 'tglSelesai'];
        $data = [];
        foreach ($allowed as $k) {
            if (array_key_exists($k, $p)) {
                $data[$k] = $p[$k];
            }
        }
        if (isset($data['tglSelesai'])) {
            $data['tglSelesai'] = RowSerializer::coerceMysqlDateTime($data['tglSelesai']);
        }
        if ($data === []) {
            return response()->json(['success' => true]);
        }
        $canManage = RoleHelper::hasPermission($jwt, 'manage_tasks');
        $isFieldStaff = in_array(RoleHelper::resolveRoleKey($jwt['roleKey'] ?? $jwt['role'] ?? ''), ['teknisi', 'penagih', 'tekpen'], true);
        if (! $canManage && ! $isFieldStaff) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        if (! $canManage && $isFieldStaff) {
            $allowedField = ['status' => 1, 'catatanTeknisi' => 1, 'tglSelesai' => 1];
            foreach (array_keys($data) as $k) {
                if (! isset($allowedField[$k])) {
                    return response()->json(['error' => 'Akses ditolak'], 403);
                }
            }
        }
        DB::table('tugas_teknisi')->where('id', $id)->update(RowSerializer::serializeForDb($data));

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::hasPermission($jwt, 'manage_tasks')) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        DB::table('tugas_teknisi')->where('id', $id)->delete();

        return response()->json(['success' => true]);
    }
}
