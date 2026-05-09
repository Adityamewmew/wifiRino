<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Api\Public\PublicApiController;
use App\Http\Controllers\Controller;
use App\Services\StaffAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private StaffAuthService $staffAuth) {}

    public function showLogin(): View
    {
        $config = $this->publicPengaturan();

        return view('auth.login', [
            'sidebarLogo' => $config['sidebar_logo_data'] ?? '',
            'waUrl' => $this->waMeUrl($config['payment_wa_cs'] ?? ''),
        ]);
    }

    private function waMeUrl(?string $raw): string
    {
        $digits = preg_replace('/\D/', '', (string) $raw);
        if ($digits === '') {
            return '#';
        }
        $n = $digits;
        if (str_starts_with($n, '0')) {
            $n = '62'.substr($n, 1);
        } elseif (! str_starts_with($n, '62')) {
            $n = '62'.$n;
        }

        return 'https://wa.me/'.$n;
    }

    public function staffLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $result = $this->staffAuth->attemptLogin(
            $request->input('email'),
            $request->input('password')
        );

        if ($result === null) {
            $user = DB::table('users')->where('email', $request->input('email'))->first();
            if ($user && (int) ($user->aktif ?? 1) === 0) {
                return back()->withErrors(['email' => 'Akun dinonaktifkan. Hubungi administrator.'])->onlyInput('email');
            }

            return back()->withErrors(['email' => 'Email atau kata sandi salah.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('staff_token', $result['token']);
        $request->session()->put('staff_user', $result['user']);

        if (! $request->boolean('remember')) {
            $request->session()->put('staff_ephemeral', true);
        } else {
            $request->session()->forget('staff_ephemeral');
        }

        $path = $this->staffAuth->redirectPathForUser($result['user']);
        if ($path === '/login') {
            return back()->withErrors(['email' => 'Hak akses belum diatur sistem untuk akun ini.'])->onlyInput('email');
        }

        return redirect()->intended($path);
    }

    public function portalLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'pelId' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $sub = app(PublicApiController::class)->clientLogin($request);
        $data = $sub->getData(true);
        if ($sub->status() !== 200 || empty($data['success'])) {
            $msg = $data['error'] ?? 'ID Pelanggan atau Nomor WA tidak ditemukan';

            return back()->withErrors(['pelId' => $msg])->onlyInput('pelId');
        }

        $request->session()->put('portal_customer', $data['customer']);
        if (! $request->boolean('remember')) {
            $request->session()->put('portal_ephemeral', true);
        } else {
            $request->session()->forget('portal_ephemeral');
        }

        return redirect('/portal-pelanggan');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /** @return array<string, string> */
    private function publicPengaturan(): array
    {
        $rows = DB::table('pengaturan')->get();
        $config = [];
        foreach ($rows as $r) {
            $config[$r->kunci] = $r->nilai;
        }
        unset($config['integration_payment_gateway']);

        return $config;
    }
}
