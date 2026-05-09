<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Support\RowSerializer;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaketWebController extends Controller
{
    public function index(): View
    {
        $rows = DB::table('paket')->orderBy('nama')->get();
        $paketList = $rows->map(fn ($r) => RowSerializer::deserializeRow((array) $r))->all();

        return view('admin.paket.index', compact('paketList') + ['activeMenu' => 'paket']);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = session('staff_user', []);
        $nama = trim($request->input('nama', ''));
        $harga = (float) $request->input('harga', 0);
        $deskripsi = trim($request->input('deskripsi', ''));
        $aktif = $request->boolean('aktif') ? 1 : 0;

        if ($nama === '' || $harga <= 0) {
            return back()->with('error', 'Nama dan harga paket wajib diisi.')->withInput();
        }

        $exists = DB::table('paket')->whereRaw('LOWER(nama) = ?', [strtolower($nama)])->exists();
        if ($exists) {
            return back()->with('error', 'Paket "' . $nama . '" sudah ada.')->withInput();
        }

        $id = (string) Str::uuid();
        DB::table('paket')->insert([
            'id' => $id,
            'nama' => $nama,
            'harga' => $harga,
            'deskripsi' => $deskripsi,
            'aktif' => $aktif,
            'createdAt' => now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'tanggal' => now(),
            'userEmail' => $user['email'] ?? '',
            'userRole' => $user['role'] ?? '',
            'aksi' => 'CREATE',
            'entitas' => 'paket',
            'idData' => $id,
            'keterangan' => 'Menambahkan paket: ' . $nama,
        ]);

        return redirect('/paket')->with('success', 'Paket "' . $nama . '" berhasil ditambahkan.');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $user = session('staff_user', []);
        $nama = trim($request->input('nama', ''));
        $harga = (float) $request->input('harga', 0);
        $deskripsi = trim($request->input('deskripsi', ''));
        $aktif = $request->boolean('aktif') ? 1 : 0;

        if ($nama === '' || $harga <= 0) {
            return back()->with('error', 'Nama dan harga paket wajib diisi.');
        }

        $dup = DB::table('paket')->whereRaw('LOWER(nama) = ?', [strtolower($nama)])->where('id', '<>', $id)->exists();
        if ($dup) {
            return back()->with('error', 'Paket "' . $nama . '" sudah ada.');
        }

        DB::table('paket')->where('id', $id)->update([
            'nama' => $nama,
            'harga' => $harga,
            'deskripsi' => $deskripsi,
            'aktif' => $aktif,
        ]);

        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'tanggal' => now(),
            'userEmail' => $user['email'] ?? '',
            'userRole' => $user['role'] ?? '',
            'aksi' => 'UPDATE',
            'entitas' => 'paket',
            'idData' => $id,
            'keterangan' => 'Memperbarui paket: ' . $nama,
        ]);

        return redirect('/paket')->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $user = session('staff_user', []);
        $roleKey = $user['roleKey'] ?? $user['role'] ?? '';
        if ($roleKey !== 'owner') {
            return back()->with('error', 'Hanya Owner yang bisa menghapus paket.');
        }

        $row = DB::table('paket')->where('id', $id)->first();
        if (!$row) {
            return back()->with('error', 'Paket tidak ditemukan.');
        }

        DB::table('paket')->where('id', $id)->delete();

        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'tanggal' => now(),
            'userEmail' => $user['email'] ?? '',
            'userRole' => $user['role'] ?? '',
            'aksi' => 'DELETE',
            'entitas' => 'paket',
            'idData' => $id,
            'keterangan' => 'Menghapus paket: ' . ($row->nama ?? ''),
        ]);

        return redirect('/paket')->with('success', 'Paket berhasil dihapus.');
    }
}
