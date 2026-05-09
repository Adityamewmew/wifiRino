<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Services\BillingCollectionService;
use App\Support\PelangganBillingHelper;
use App\Support\RoleHelper;
use App\Support\RowSerializer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    public function index(Request $request, string $collection)
    {
        $jwt = $request->attributes->get('jwt_user');
        $table = BillingCollectionService::getTable($collection);
        if (! $table) {
            return response()->json(['error' => 'Invalid collection'], 400);
        }
        if ($table === 'pembukuan' && ! RoleHelper::canViewFinanceTotals($jwt)) {
            return response()->json(['error' => 'Akses ditolak untuk melihat pembukuan.'], 403);
        }
        if ($table === 'users' && ! RoleHelper::hasPermission($jwt, 'manage_users')) {
            return response()->json(['error' => 'Akses ditolak untuk melihat data karyawan.'], 403);
        }
        if ($table === 'pengumuman' && ! RoleHelper::hasPermission($jwt, 'access_admin_app')) {
            return response()->json(['error' => 'Akses ditolak untuk melihat data siaran.'], 403);
        }

        $allowed = BillingCollectionService::getColumnSet($table);
        $q = DB::table($table);
        foreach ($request->query() as $key => $value) {
            if ($key === 'orderBy' || $key === 'orderDir') {
                continue;
            }
            if ($table === 'pembukuan' && ($key === 'bulan' || $key === 'tahun')) {
                continue;
            }
            if (! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', (string) $key)) {
                continue;
            }
            if (! isset($allowed[$key])) {
                continue;
            }
            $q->where($key, $value);
        }
        if ($table === 'pembukuan' && $request->query('tahun')) {
            $y = $request->query('tahun');
            $m = $request->query('bulan') ? str_pad((string) $request->query('bulan'), 2, '0', STR_PAD_LEFT) : '';
            if ($m !== '') {
                $q->where('tanggal', 'like', "{$y}-{$m}-%");
            } else {
                $q->where('tanggal', 'like', "{$y}-%");
            }
        }
        $rows = $q->get();
        $results = $rows->map(fn ($row) => RowSerializer::deserializeRow((array) $row))->all();
        $results = BillingCollectionService::injectBayarDimuka($results, $table);

        return response()->json($results);
    }

    public function show(Request $request, string $collection, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        $table = BillingCollectionService::getTable($collection);
        if (! $table) {
            return response()->json(['error' => 'Invalid collection'], 400);
        }
        if ($table === 'pembukuan' && ! RoleHelper::canViewFinanceTotals($jwt)) {
            return response()->json(['error' => 'Akses ditolak untuk melihat pembukuan.'], 403);
        }
        if ($table === 'users' && ! RoleHelper::hasPermission($jwt, 'manage_users')) {
            return response()->json(['error' => 'Akses ditolak untuk melihat data karyawan.'], 403);
        }
        if ($table === 'pengumuman' && ! RoleHelper::hasPermission($jwt, 'access_admin_app')) {
            return response()->json(['error' => 'Akses ditolak untuk melihat data siaran.'], 403);
        }
        $row = DB::table($table)->where('id', $id)->first();
        if (! $row) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        return response()->json(RowSerializer::deserializeRow((array) $row));
    }

    public function store(Request $request, string $collection)
    {
        $jwt = $request->attributes->get('jwt_user');
        $table = BillingCollectionService::getTable($collection);
        if (! $table) {
            return response()->json(['error' => 'Invalid collection'], 400);
        }
        if (! BillingCollectionService::canWriteCollection($table, $jwt)) {
            return response()->json(['error' => 'Akses ditolak untuk menambah data pada koleksi ini.'], 403);
        }
        if ($table === 'pembukuan' && ! RoleHelper::canViewFinanceTotals($jwt)) {
            return response()->json(['error' => 'Akses ditolak untuk menambah pembukuan.'], 403);
        }
        if ($table === 'users' && ! RoleHelper::hasPermission($jwt, 'manage_users')) {
            return response()->json(['error' => 'Akses ditolak untuk menambah karyawan.'], 403);
        }
        if ($table === 'pengumuman' && ! RoleHelper::hasPermission($jwt, 'access_admin_app')) {
            return response()->json(['error' => 'Akses ditolak untuk membuat siaran.'], 403);
        }

        $data = BillingCollectionService::filterAllowedColumns($table, $request->all());
        if ($data === []) {
            return response()->json(['error' => 'Payload tidak valid atau tidak mengandung kolom yang diizinkan'], 400);
        }
        if ($table === 'pelanggan') {
            PelangganBillingHelper::normalizePelangganNominalFields($data);
        }
        $err = BillingCollectionService::ensureUniqueNamaIfNeeded($table, $data, null);
        if ($err) {
            return response()->json(['error' => $err], 409);
        }
        if (empty($data['id'])) {
            $data['id'] = (string) Str::uuid();
        }
        if ($table === 'users' && array_key_exists('areas', $data) && is_array($data['areas'])) {
            $data['areas'] = json_encode($data['areas'], JSON_UNESCAPED_UNICODE);
        }
        if ($table === 'users' && ! empty($data['password'])) {
            $data['password'] = password_hash((string) $data['password'], PASSWORD_BCRYPT, ['cost' => 10]);
        }
        $data = RowSerializer::serializeForDb($data);
        try {
            DB::table($table)->insert($data);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), 'UNIQUE')) {
                if ($table === 'areas') {
                    return response()->json(['error' => 'Nama area sudah terdaftar. Gunakan nama lain.'], 409);
                }
                if ($table === 'paket') {
                    return response()->json(['error' => 'Nama paket sudah terdaftar. Gunakan nama lain.'], 409);
                }
            }
            throw $e;
        }
        if (! empty($jwt['email'])) {
            DB::table('audit_logs')->insert([
                'id' => (string) Str::uuid(),
                'tanggal' => now(),
                'userEmail' => $jwt['email'],
                'userRole' => $jwt['role'] ?? '',
                'aksi' => 'CREATE',
                'entitas' => $table,
                'idData' => $data['id'],
                'keterangan' => 'Menambahkan data baru',
            ]);
        }

        return response()->json(array_merge(['id' => $data['id']], RowSerializer::deserializeRow($data)));
    }

    public function update(Request $request, string $collection, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        $table = BillingCollectionService::getTable($collection);
        if (! $table) {
            return response()->json(['error' => 'Invalid collection'], 400);
        }
        if (! BillingCollectionService::canWriteCollection($table, $jwt)) {
            return response()->json(['error' => 'Akses ditolak untuk mengubah data pada koleksi ini.'], 403);
        }
        if ($table === 'pembukuan' && ! RoleHelper::canViewFinanceTotals($jwt)) {
            return response()->json(['error' => 'Akses ditolak untuk mengubah pembukuan.'], 403);
        }
        if ($table === 'users' && ! RoleHelper::hasPermission($jwt, 'manage_users')) {
            return response()->json(['error' => 'Akses ditolak untuk mengubah data karyawan.'], 403);
        }
        if ($table === 'pengumuman' && ! RoleHelper::hasPermission($jwt, 'access_admin_app')) {
            return response()->json(['error' => 'Akses ditolak untuk mengubah siaran.'], 403);
        }

        $data = BillingCollectionService::filterAllowedColumns($table, $request->all());
        if ($data === []) {
            return response()->json(['error' => 'Payload tidak valid atau tidak mengandung kolom yang diizinkan'], 400);
        }
        if ($table === 'pelanggan') {
            PelangganBillingHelper::normalizePelangganNominalFields($data);
        }
        $err = BillingCollectionService::ensureUniqueNamaIfNeeded($table, $data, $id);
        if ($err) {
            return response()->json(['error' => $err], 409);
        }
        if ($table === 'users' && array_key_exists('areas', $data) && is_array($data['areas'])) {
            $data['areas'] = json_encode($data['areas'], JSON_UNESCAPED_UNICODE);
        }
        if ($table === 'users' && ! empty($data['password'])) {
            $data['password'] = password_hash((string) $data['password'], PASSWORD_BCRYPT, ['cost' => 10]);
        }
        if ($table === 'pelanggan') {
            $data['updatedAt'] = now()->format('Y-m-d H:i:s');
        }
        $data = RowSerializer::serializeForDb($data);
        try {
            DB::table($table)->where('id', $id)->update($data);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), 'UNIQUE')) {
                if ($table === 'areas') {
                    return response()->json(['error' => 'Nama area sudah terdaftar. Gunakan nama lain.'], 409);
                }
                if ($table === 'paket') {
                    return response()->json(['error' => 'Nama paket sudah terdaftar. Gunakan nama lain.'], 409);
                }
            }
            throw $e;
        }
        if (! empty($jwt['email'])) {
            DB::table('audit_logs')->insert([
                'id' => (string) Str::uuid(),
                'tanggal' => now(),
                'userEmail' => $jwt['email'],
                'userRole' => $jwt['role'] ?? '',
                'aksi' => 'UPDATE',
                'entitas' => $table,
                'idData' => $id,
                'keterangan' => 'Memperbarui data',
            ]);
        }

        return response()->json(['success' => true, 'id' => $id]);
    }

    public function destroy(Request $request, string $collection, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        $table = BillingCollectionService::getTable($collection);
        if (! $table) {
            return response()->json(['error' => 'Invalid collection'], 400);
        }
        if (in_array($table, ['pelanggan', 'paket', 'areas'], true) && ! RoleHelper::isOwnerUser($jwt)) {
            return response()->json(['error' => 'Akses ditolak. Hapus data pelanggan/paket/area hanya untuk Owner.'], 403);
        }
        if (! BillingCollectionService::canWriteCollection($table, $jwt)) {
            return response()->json(['error' => 'Akses ditolak untuk menghapus data pada koleksi ini.'], 403);
        }
        if ($table === 'pembukuan' && ! RoleHelper::canViewFinanceTotals($jwt)) {
            return response()->json(['error' => 'Akses ditolak untuk menghapus pembukuan.'], 403);
        }
        if ($table === 'users' && ! RoleHelper::hasPermission($jwt, 'manage_users')) {
            return response()->json(['error' => 'Akses ditolak untuk menghapus data karyawan.'], 403);
        }
        if ($table === 'pengumuman' && ! RoleHelper::hasPermission($jwt, 'access_admin_app')) {
            return response()->json(['error' => 'Akses ditolak untuk menghapus siaran.'], 403);
        }
        try {
            BillingCollectionService::deleteCollectionRecord($table, $id, $jwt);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'id' => $id]);
    }
}
