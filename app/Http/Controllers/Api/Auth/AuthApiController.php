<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\StaffAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthApiController extends Controller
{
    public function __construct(private StaffAuthService $staffAuth) {}

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = (string) $request->input('password', '');
        $user = DB::table('users')->where('email', $email)->first();
        if (! $user) {
            return response()->json(['error' => 'User tidak ditemukan'], 401);
        }
        if ((int) ($user->aktif ?? 1) === 0) {
            return response()->json(['error' => 'Akun dinonaktifkan. Hubungi administrator.'], 403);
        }
        $result = $this->staffAuth->attemptLogin((string) $email, $password);
        if ($result === null) {
            return response()->json(['error' => 'Password salah'], 401);
        }

        return response()->json([
            'token' => $result['token'],
            'user' => $result['user'],
        ]);
    }

    public function me(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        $uid = $jwt['uid'] ?? null;
        if (! $uid) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = DB::table('users')->select('id', 'nama', 'email', 'role', 'aktif', 'areas')->where('id', $uid)->first();
        if (! $user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }

        return response()->json($this->staffAuth->profileFromRow((array) $user));
    }

    public function verifyPassword(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        $password = trim((string) $request->input('password', ''));
        if ($password === '') {
            return response()->json(['error' => 'Password wajib diisi'], 400);
        }
        $uid = $jwt['uid'] ?? null;
        $row = DB::table('users')->select('password')->where('id', $uid)->first();
        if (! $row || ! $row->password) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }
        if (! password_verify($password, (string) $row->password)) {
            return response()->json(['error' => 'Password salah'], 401);
        }

        return response()->json(['success' => true]);
    }
}
