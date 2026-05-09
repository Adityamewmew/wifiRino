<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\BillingCollectionService;
use App\Support\RowSerializer;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AreaWebController extends Controller
{
    public function index(): View
    {
        $rows = DB::table('areas')->orderBy('nama')->get();
        $areas = $rows->map(fn ($r) => RowSerializer::deserializeRow((array) $r))->all();

        return view('admin.area.index', compact('areas') + ['activeMenu' => 'area']);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = session('staff_user', []);
        $nama = trim($request->input('nama', ''));
        $keterangan = trim($request->input('keterangan', ''));

        if ($nama === '') {
            return back()->with('error', 'Nama area tidak boleh kosong.')->withInput();
        }

        $exists = DB::table('areas')->whereRaw('LOWER(nama) = ?', [strtolower($nama)])->exists();
        if ($exists) {
            return back()->with('error', 'Area "' . $nama . '" sudah ada.')->withInput();
        }

        $id = (string) Str::uuid();
        DB::table('areas')->insert([
            'id' => $id,
            'nama' => $nama,
            'keterangan' => $keterangan,
            'createdAt' => now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'tanggal' => now(),
            'userEmail' => $user['email'] ?? '',
            'userRole' => $user['role'] ?? '',
            'aksi' => 'CREATE',
            'entitas' => 'areas',
            'idData' => $id,
            'keterangan' => 'Menambahkan area: ' . $nama,
        ]);

        return redirect('/area')->with('success', 'Area "' . $nama . '" berhasil ditambahkan.');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $user = session('staff_user', []);
        $nama = trim($request->input('nama', ''));
        $keterangan = trim($request->input('keterangan', ''));

        if ($nama === '') {
            return back()->with('error', 'Nama area tidak boleh kosong.');
        }

        $dup = DB::table('areas')->whereRaw('LOWER(nama) = ?', [strtolower($nama)])->where('id', '<>', $id)->exists();
        if ($dup) {
            return back()->with('error', 'Area "' . $nama . '" sudah ada.');
        }

        DB::table('areas')->where('id', $id)->update([
            'nama' => $nama,
            'keterangan' => $keterangan,
        ]);

        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'tanggal' => now(),
            'userEmail' => $user['email'] ?? '',
            'userRole' => $user['role'] ?? '',
            'aksi' => 'UPDATE',
            'entitas' => 'areas',
            'idData' => $id,
            'keterangan' => 'Memperbarui area: ' . $nama,
        ]);

        return redirect('/area')->with('success', 'Area berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $user = session('staff_user', []);
        $roleKey = $user['roleKey'] ?? $user['role'] ?? '';
        if ($roleKey !== 'owner') {
            return back()->with('error', 'Hanya Owner yang bisa menghapus area.');
        }

        $row = DB::table('areas')->where('id', $id)->first();
        if (!$row) {
            return back()->with('error', 'Area tidak ditemukan.');
        }

        DB::table('areas')->where('id', $id)->delete();

        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'tanggal' => now(),
            'userEmail' => $user['email'] ?? '',
            'userRole' => $user['role'] ?? '',
            'aksi' => 'DELETE',
            'entitas' => 'areas',
            'idData' => $id,
            'keterangan' => 'Menghapus area: ' . ($row->nama ?? ''),
        ]);

        return redirect('/area')->with('success', 'Area berhasil dihapus.');
    }
}
