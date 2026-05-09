<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Services\StaffAuthService;
use App\Support\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDirectoryController extends Controller
{
    public function __construct(private StaffAuthService $staffAuth) {}

    public function taskAssignees(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::hasPermission($jwt, 'access_admin_app') && ! RoleHelper::hasPermission($jwt, 'manage_tasks')) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $rows = DB::table('users')->where('aktif', 1)->orderBy('nama')->get();
        $out = [];
        foreach ($rows as $u) {
            $profile = $this->staffAuth->profileFromRow((array) $u);
            $key = RoleHelper::resolveRoleKey($profile['roleKey'] ?? $profile['role'] ?? '');
            if (! in_array($key, ['teknisi', 'penagih', 'tekpen'], true)) {
                continue;
            }
            $out[] = [
                'id' => $profile['id'],
                'nama' => $profile['nama'],
                'email' => $profile['email'],
                'role' => $profile['role'],
                'roleKey' => $profile['roleKey'],
            ];
        }

        return response()->json($out);
    }
}
