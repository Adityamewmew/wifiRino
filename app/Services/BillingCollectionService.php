<?php

namespace App\Services;

use App\Support\RoleHelper;
use App\Support\RowSerializer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BillingCollectionService
{
    private const VALID = ['pelanggan', 'tagihan_bulanan', 'areas', 'paket', 'users', 'pembukuan', 'pengumuman'];

    private const ADMIN_WRITABLE = ['pelanggan', 'tagihan_bulanan', 'areas', 'paket', 'pengumuman'];

    private const COLUMN_KEY_RE = '/^[A-Za-z_][A-Za-z0-9_]*$/';

    public static function getTable(?string $collection): ?string
    {
        if ($collection === null) {
            return null;
        }

        return in_array($collection, self::VALID, true) ? $collection : null;
    }

    /** @return array<string,int> map column => 1 */
    public static function getColumnSet(string $table): array
    {
        return array_flip(Schema::getColumnListing($table));
    }

    public static function canWriteCollection(string $table, array $jwt): bool
    {
        if ($table === 'users') {
            return RoleHelper::hasPermission($jwt, 'manage_users');
        }
        if ($table === 'pembukuan') {
            return RoleHelper::canViewFinanceTotals($jwt);
        }
        if (in_array($table, self::ADMIN_WRITABLE, true)) {
            return RoleHelper::hasPermission($jwt, 'access_admin_app');
        }

        return false;
    }

    /** @param array<string,mixed> $payload */
    public static function filterAllowedColumns(string $table, array $payload): array
    {
        $cols = self::getColumnSet($table);
        $out = [];
        foreach ($payload as $k => $v) {
            $key = trim((string) $k);
            if (! preg_match(self::COLUMN_KEY_RE, $key)) {
                continue;
            }
            if (! isset($cols[$key])) {
                continue;
            }
            $out[$key] = $v;
        }

        return $out;
    }

    /** @param array<string,mixed> $payload */
    public static function ensureUniqueNamaIfNeeded(string $table, array $payload, ?string $currentId): ?string
    {
        if (! in_array($table, ['areas', 'paket'], true)) {
            return null;
        }
        if (! array_key_exists('nama', $payload)) {
            return null;
        }
        $nama = trim((string) $payload['nama']);
        if ($nama === '') {
            return null;
        }
        $q = DB::table($table)->whereRaw('LOWER(TRIM(nama)) = LOWER(TRIM(?))', [$nama]);
        if ($currentId) {
            $q->where('id', '<>', $currentId);
        }
        if ($q->exists()) {
            $ent = $table === 'areas' ? 'Area' : 'Paket';

            return "{$ent} \"{$nama}\" sudah ada. Gunakan nama lain atau edit data yang sudah ada.";
        }

        return null;
    }

    /**
     * @param  list<array<string,mixed>>  $results
     * @return list<array<string,mixed>>
     */
    public static function injectBayarDimuka(array $results, string $table): array
    {
        if ($table !== 'pelanggan' && $table !== 'tagihan_bulanan') {
            return $results;
        }
        $currM = (int) date('n');
        $currY = (int) date('Y');
        $future = DB::table('tagihan_bulanan')
            ->where('status', 'lunas')
            ->where(function ($q) use ($currY, $currM) {
                $q->where('tahun', '>', $currY)
                    ->orWhere(function ($q2) use ($currY, $currM) {
                        $q2->where('tahun', $currY)->where('bulan', '>', $currM);
                    });
            })
            ->pluck('idPelanggan');
        $dimukaCount = [];
        foreach ($future as $idPel) {
            $k = trim((string) $idPel);
            if ($k === '') {
                continue;
            }
            $dimukaCount[$k] = ($dimukaCount[$k] ?? 0) + 1;
        }
        foreach ($results as &$r) {
            $pelId = trim((string) ($r['idPelanggan'] ?? $r['id'] ?? ''));
            if ($pelId !== '' && isset($dimukaCount[$pelId])) {
                $r['bayarDimuka'] = $dimukaCount[$pelId];
            }
        }

        return $results;
    }

    public static function deleteCollectionRecord(string $table, string $id, ?array $actor): void
    {
        if ($table === 'pelanggan') {
            $pelRow = DB::table('pelanggan')->where('id', $id)->first();
            if ($pelRow) {
                $d = RowSerializer::deserializeRow((array) $pelRow) ?? [];
                $pelId = $d['idPelanggan'] ?? $d['id'] ?? null;
                DB::table('pelanggan_mikrotik')->where('pelangganDbId', $id)->delete();
                if ($pelId) {
                    DB::table('tagihan_bulanan')->where('idPelanggan', $pelId)->delete();
                    DB::table('push_tokens')
                        ->whereRaw("LOWER(COALESCE(targetType, '')) = 'pelanggan'")
                        ->where('targetId', $pelId)
                        ->delete();
                    DB::table('pembukuan')->whereIn('kategori', ['Tagihan Internet', 'Bayar Dimuka'])
                        ->where('keterangan', 'like', '%'.$pelId.'%')
                        ->delete();
                }
            }
        }
        DB::table($table)->where('id', $id)->delete();
        if ($actor && ! empty($actor['email'])) {
            DB::table('audit_logs')->insert([
                'id' => (string) Str::uuid(),
                'tanggal' => now(),
                'userEmail' => $actor['email'],
                'userRole' => $actor['role'] ?? '',
                'aksi' => 'DELETE',
                'entitas' => $table,
                'idData' => $id,
                'keterangan' => $table === 'pelanggan' ? 'Menghapus pelanggan beserta tagihan dan data terkait' : 'Menghapus data',
            ]);
        }
    }
}
