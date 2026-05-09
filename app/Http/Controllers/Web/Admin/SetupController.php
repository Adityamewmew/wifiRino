<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SetupController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (DB::table('users')->count() > 0) {
            return redirect()->route('login')->with('info', 'Sistem sudah memiliki pengguna. Login dengan akun yang ada.');
        }

        $exists = DB::table('users')->where('email', 'admin@sans-speed.local')->exists();
        if (! $exists) {
            DB::table('users')->insert([
                'id' => (string) Str::uuid(),
                'nama' => 'Super Admin Pertama',
                'email' => 'admin@sans-speed.local',
                'password' => password_hash('admin', PASSWORD_BCRYPT),
                'noWA' => null,
                'role' => 'owner',
                'gaji' => 0,
                'aktif' => 1,
                'areas' => null,
                'createdAt' => now(),
            ]);
        }

        return view('billing.setup-admin-done');
    }
}
