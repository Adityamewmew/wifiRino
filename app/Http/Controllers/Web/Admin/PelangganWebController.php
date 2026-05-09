<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\BillingCollectionService;
use App\Support\PelangganBillingHelper;
use App\Support\RoleHelper;
use App\Support\RowSerializer;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PelangganWebController extends Controller
{
    public function index(Request $request): View
    {
        $user = session('staff_user', []);

        $fArea = $request->query('area', 'all');
        $fStatus = $request->query('status', 'all');
        $fPaket = $request->query('paket', 'all');
        $fSearch = trim($request->query('q', ''));

        $query = DB::table('pelanggan');

        if ($fArea !== 'all' && $fArea !== '') {
            $query->where('area', $fArea);
        }
        if ($fStatus !== 'all' && $fStatus !== '') {
            $query->where('status', $fStatus);
        } else {
            $query->where(function ($w) {
                $w->whereNull('status')->orWhere('status', '<>', 'berhenti');
            });
        }
        if ($fPaket !== 'all' && $fPaket !== '') {
            $query->where('paket', $fPaket);
        }
        if ($fSearch !== '') {
            $like = '%' . addcslashes($fSearch, '%_\\') . '%';
            $query->where(function ($w) use ($like) {
                $w->where('nama', 'like', $like)
                    ->orWhere('idPelanggan', 'like', $like)
                    ->orWhere('idPPOE', 'like', $like)
                    ->orWhere('noWA', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('noKtp', 'like', $like);
            });
        }

        $rows = $query->orderBy('nama')->get();
        $pelanggan = $rows->map(fn ($row) => RowSerializer::deserializeRow((array) $row))->all();

        // Stats
        $sAktif = $rows->filter(fn ($r) => ($r->status ?? '') === 'aktif')->count();
        $sIsolir = $rows->filter(fn ($r) => ($r->status ?? '') === 'isolir')->count();
        $sBerhenti = DB::table('pelanggan')->where('status', 'berhenti')->count();
        $sTotal = DB::table('pelanggan')->count();

        // Area list for filter
        $areas = DB::table('areas')->orderBy('nama')->pluck('nama')->all();

        // Paket list for filter + form
        $paketAll = DB::table('paket')->orderBy('nama')->get()
            ->map(fn ($r) => RowSerializer::deserializeRow((array) $r))->all();
        $paketAktif = array_filter($paketAll, fn ($p) => ($p['aktif'] ?? 1) == 1);

        return view('admin.pelanggan.index', array_merge(compact(
            'pelanggan', 'areas', 'paketAll', 'paketAktif',
            'sTotal', 'sAktif', 'sIsolir', 'sBerhenti',
            'fArea', 'fStatus', 'fPaket', 'fSearch'
        ), ['activeMenu' => 'pelanggan']));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = session('staff_user', []);

        $data = $request->only([
            'idPelanggan', 'nama', 'noWA', 'area', 'paket', 'hargaPaket',
            'tglTagih', 'alamat', 'status', 'totalFinal', 'idPPOE',
            'email', 'noKtp', 'tanggalMulaiStr', 'mulaiTagihan', 'keterangan',
            'foto1', 'latitude', 'longitude', 'lamaBerlanggananTeks',
            'biayaTambahan1_rincian', 'biayaTambahan1_nominal',
            'biayaTambahan2_rincian', 'biayaTambahan2_nominal',
            'diskon_keterangan', 'diskon_nominal',
        ]);

        // Build structured fields
        $payload = [];
        foreach (['idPelanggan', 'nama', 'noWA', 'area', 'paket', 'tglTagih', 'alamat',
                   'status', 'idPPOE', 'email', 'noKtp', 'tanggalMulaiStr', 'mulaiTagihan',
                   'keterangan', 'foto1', 'latitude', 'longitude', 'lamaBerlanggananTeks'] as $f) {
            if (isset($data[$f]) && $data[$f] !== '') {
                $payload[$f] = $data[$f];
            }
        }
        $payload['hargaPaket'] = (float) ($data['hargaPaket'] ?? 0);
        $payload['totalFinal'] = (float) ($data['totalFinal'] ?? $payload['hargaPaket']);
        $payload['status'] = $data['status'] ?? 'aktif';

        // Biaya tambahan & diskon as JSON
        $b1r = trim($data['biayaTambahan1_rincian'] ?? '');
        $b1n = (float) ($data['biayaTambahan1_nominal'] ?? 0);
        if ($b1r || $b1n > 0) {
            $payload['biayaTambahan1'] = json_encode(['rincian' => $b1r ?: 'Biaya tambahan 1', 'nominal' => $b1n]);
        }
        $b2r = trim($data['biayaTambahan2_rincian'] ?? '');
        $b2n = (float) ($data['biayaTambahan2_nominal'] ?? 0);
        if ($b2r || $b2n > 0) {
            $payload['biayaTambahan2'] = json_encode(['rincian' => $b2r ?: 'Biaya tambahan 2', 'nominal' => $b2n]);
        }
        $dk = trim($data['diskon_keterangan'] ?? '');
        $dn = (float) ($data['diskon_nominal'] ?? 0);
        if ($dk || $dn > 0) {
            $payload['diskon'] = json_encode(['keterangan' => $dk ?: 'Diskon', 'nominal' => $dn]);
        }

        // Auto-generate ID if empty
        if (empty($payload['idPelanggan'])) {
            $last = DB::table('pelanggan')->max('idPelanggan');
            $num = 1;
            if ($last && preg_match('/(\d+)$/', $last, $m)) {
                $num = ((int) $m[1]) + 1;
            }
            $payload['idPelanggan'] = 'SS-' . str_pad($num, 4, '0', STR_PAD_LEFT);
        }

        // Parse tanggalMulaiStr to bulanMulai/tahunMulai
        if (!empty($payload['tanggalMulaiStr'])) {
            $parts = explode('-', $payload['tanggalMulaiStr']);
            if (count($parts) >= 2) {
                $payload['bulanMulai'] = (int) $parts[1];
                $payload['tahunMulai'] = (int) $parts[0];
            }
        }

        $allowed = BillingCollectionService::filterAllowedColumns('pelanggan', $payload);
        if (empty($allowed)) {
            return back()->with('error', 'Data tidak valid.')->withInput();
        }

        PelangganBillingHelper::normalizePelangganNominalFields($allowed);

        $allowed['id'] = (string) Str::uuid();
        $allowed['createdAt'] = now()->format('Y-m-d H:i:s');
        $allowed['updatedAt'] = now()->format('Y-m-d H:i:s');

        $serialized = RowSerializer::serializeForDb($allowed);

        try {
            DB::table('pelanggan')->insert($serialized);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), 'UNIQUE')) {
                return back()->with('error', 'ID Pelanggan sudah terdaftar.')->withInput();
            }
            throw $e;
        }

        // Audit log
        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'tanggal' => now(),
            'userEmail' => $user['email'] ?? '',
            'userRole' => $user['role'] ?? '',
            'aksi' => 'CREATE',
            'entitas' => 'pelanggan',
            'idData' => $allowed['id'],
            'keterangan' => 'Menambahkan pelanggan baru: ' . ($payload['nama'] ?? ''),
        ]);

        return redirect('/pelanggan')->with('success', 'Pelanggan "' . ($payload['nama'] ?? '') . '" berhasil ditambahkan.');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $user = session('staff_user', []);

        $existing = DB::table('pelanggan')->where('id', $id)->first();
        if (!$existing) {
            return back()->with('error', 'Pelanggan tidak ditemukan.');
        }

        $data = $request->only([
            'idPelanggan', 'nama', 'noWA', 'area', 'paket', 'hargaPaket',
            'tglTagih', 'alamat', 'status', 'totalFinal', 'idPPOE',
            'email', 'noKtp', 'tanggalMulaiStr', 'mulaiTagihan', 'keterangan',
            'foto1', 'latitude', 'longitude', 'lamaBerlanggananTeks',
            'biayaTambahan1_rincian', 'biayaTambahan1_nominal',
            'biayaTambahan2_rincian', 'biayaTambahan2_nominal',
            'diskon_keterangan', 'diskon_nominal',
        ]);

        $payload = [];
        foreach (['idPelanggan', 'nama', 'noWA', 'area', 'paket', 'tglTagih', 'alamat',
                   'status', 'idPPOE', 'email', 'noKtp', 'tanggalMulaiStr', 'mulaiTagihan',
                   'keterangan', 'foto1', 'latitude', 'longitude', 'lamaBerlanggananTeks'] as $f) {
            if (array_key_exists($f, $data)) {
                $payload[$f] = $data[$f] ?? '';
            }
        }
        $payload['hargaPaket'] = (float) ($data['hargaPaket'] ?? 0);
        $payload['totalFinal'] = (float) ($data['totalFinal'] ?? $payload['hargaPaket']);

        // Biaya tambahan & diskon
        $b1r = trim($data['biayaTambahan1_rincian'] ?? '');
        $b1n = (float) ($data['biayaTambahan1_nominal'] ?? 0);
        $payload['biayaTambahan1'] = json_encode(['rincian' => $b1r, 'nominal' => $b1n]);

        $b2r = trim($data['biayaTambahan2_rincian'] ?? '');
        $b2n = (float) ($data['biayaTambahan2_nominal'] ?? 0);
        $payload['biayaTambahan2'] = json_encode(['rincian' => $b2r, 'nominal' => $b2n]);

        $dk = trim($data['diskon_keterangan'] ?? '');
        $dn = (float) ($data['diskon_nominal'] ?? 0);
        $payload['diskon'] = json_encode(['keterangan' => $dk, 'nominal' => $dn]);

        if (!empty($payload['tanggalMulaiStr'])) {
            $parts = explode('-', $payload['tanggalMulaiStr']);
            if (count($parts) >= 2) {
                $payload['bulanMulai'] = (int) $parts[1];
                $payload['tahunMulai'] = (int) $parts[0];
            }
        }

        $payload['updatedAt'] = now()->format('Y-m-d H:i:s');

        $allowed = BillingCollectionService::filterAllowedColumns('pelanggan', $payload);
        PelangganBillingHelper::normalizePelangganNominalFields($allowed);
        $serialized = RowSerializer::serializeForDb($allowed);

        try {
            DB::table('pelanggan')->where('id', $id)->update($serialized);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), 'UNIQUE')) {
                return back()->with('error', 'ID Pelanggan sudah digunakan.')->withInput();
            }
            throw $e;
        }

        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'tanggal' => now(),
            'userEmail' => $user['email'] ?? '',
            'userRole' => $user['role'] ?? '',
            'aksi' => 'UPDATE',
            'entitas' => 'pelanggan',
            'idData' => $id,
            'keterangan' => 'Memperbarui pelanggan: ' . ($payload['nama'] ?? ''),
        ]);

        return redirect('/pelanggan')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $user = session('staff_user', []);
        $roleKey = $user['roleKey'] ?? $user['role'] ?? '';
        if ($roleKey !== 'owner') {
            return back()->with('error', 'Hanya Owner yang bisa menghapus pelanggan.');
        }

        $existing = DB::table('pelanggan')->where('id', $id)->first();
        if (!$existing) {
            return back()->with('error', 'Pelanggan tidak ditemukan.');
        }

        DB::table('pelanggan')->where('id', $id)->delete();

        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'tanggal' => now(),
            'userEmail' => $user['email'] ?? '',
            'userRole' => $user['role'] ?? '',
            'aksi' => 'DELETE',
            'entitas' => 'pelanggan',
            'idData' => $id,
            'keterangan' => 'Menghapus pelanggan: ' . ($existing->nama ?? ''),
        ]);

        return redirect('/pelanggan')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
