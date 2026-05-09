<?php

namespace App\Services;

use App\Support\JwtHelper;
use App\Support\RoleHelper;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;

class StaffAuthService
{
    /** @return array{token: string, user: array}|null */
    public function attemptLogin(string $email, string $password): ?array
    {
        $user = DB::table('users')->where('email', $email)->first();
        if (! $user) {
            return null;
        }
        if ((int) ($user->aktif ?? 1) === 0) {
            return null;
        }
        if (! password_verify($password, (string) $user->password)) {
            return null;
        }
        $resolvedAreas = $this->resolveUserAreas((array) $user);
        $authUser = $this->buildAuthUserProfile((array) $user, $resolvedAreas);
        $roleKey = $authUser['roleKey'];
        $token = JWT::encode([
            'uid' => $authUser['uid'],
            'email' => $authUser['email'],
            'role' => $authUser['role'],
            'roleKey' => $roleKey,
            'permissions' => $authUser['permissions'],
        ], JwtHelper::secret(), 'HS256');

        return ['token' => $token, 'user' => $authUser];
    }

    /** Profil JWT/API dari baris users (tanpa verifikasi password). */
    public function profileFromRow(array $userRow): array
    {
        $resolvedAreas = $this->resolveUserAreas($userRow);

        return $this->buildAuthUserProfile($userRow, $resolvedAreas);
    }

    public function redirectPathForUser(array $authUser): string
    {
        if (RoleHelper::hasPermission($authUser, 'access_admin_app')) {
            return '/dashboard-admin';
        }
        $roleKey = RoleHelper::resolveRoleKey($authUser['roleKey'] ?? $authUser['role'] ?? '');
        if (in_array($roleKey, ['teknisi', 'penagih', 'tekpen', 'teknisipenagih'], true)) {
            return '/app-teknisi';
        }

        return '/login';
    }

    /** @return list<string> */
    private function resolveUserAreas(array $userRow): array
    {
        $raw = $userRow['areas'] ?? null;
        if ($raw === null || $raw === '') {
            return [];
        }
        if (is_string($raw)) {
            try {
                $p = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($p)) {
                    return array_values(array_filter(array_map('strval', $p)));
                }
            } catch (\Throwable) {
                /* ignore */
            }
        }

        return [];
    }

    /** @param list<string> $resolvedAreas */
    private function buildAuthUserProfile(array $userRow, array $resolvedAreas): array
    {
        $roleKey = RoleHelper::resolveRoleKey($userRow['role'] ?? null);

        return [
            'id' => $userRow['id'],
            'uid' => $userRow['id'],
            'email' => $userRow['email'],
            'nama' => $userRow['nama'] ?? '',
            'role' => RoleHelper::roleToCompat($roleKey),
            'roleKey' => $roleKey,
            'permissions' => RoleHelper::getPermissions($roleKey, null),
            'aktif' => $userRow['aktif'] ?? 1,
            'areas' => $resolvedAreas,
        ];
    }
}
