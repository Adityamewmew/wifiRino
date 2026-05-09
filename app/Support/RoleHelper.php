<?php

namespace App\Support;

/**
 * Port logic dari server/server.js (ROLE_*, hasPermission, dll.)
 */
class RoleHelper
{
    private const ALIAS_TO_KEY = [
        'superadmin' => 'owner',
        'admin' => 'admin_keuangan',
        'admin keuangan' => 'admin_keuangan',
        'admin-keuangan' => 'admin_keuangan',
        'adminkeuangan' => 'admin_keuangan',
        'owner' => 'owner',
        'admin_keuangan' => 'admin_keuangan',
        'admin_kasir' => 'admin_kasir',
        'admin kasir' => 'admin_kasir',
        'admin-kasir' => 'admin_kasir',
        'adminkasir' => 'admin_kasir',
        'kasir' => 'admin_kasir',
        'teknisi' => 'teknisi',
        'penagih' => 'penagih',
        'tekpen' => 'tekpen',
        'teknisipenagih' => 'tekpen',
    ];

    private const KEY_TO_COMPAT = [
        'owner' => 'superadmin',
        'admin_keuangan' => 'admin',
        'admin_kasir' => 'admin',
        'teknisi' => 'teknisi',
        'penagih' => 'penagih',
        'tekpen' => 'tekpen',
    ];

    private const PERMISSIONS = [
        'owner' => ['access_admin_app', 'view_finance_totals', 'view_finance_reports', 'collect_customer_payment', 'view_customer_bill_amount', 'manage_settings', 'manage_settings_wa', 'manage_users', 'manage_backup_audit', 'manage_tasks'],
        'admin_keuangan' => ['access_admin_app', 'view_finance_totals', 'view_finance_reports', 'collect_customer_payment', 'view_customer_bill_amount', 'manage_settings', 'manage_settings_wa', 'manage_users', 'manage_backup_audit', 'manage_tasks'],
        'admin_kasir' => ['access_admin_app', 'collect_customer_payment', 'view_customer_bill_amount', 'manage_tasks', 'manage_settings_wa'],
        'teknisi' => [],
        'penagih' => [],
        'tekpen' => [],
    ];

    public static function normText(?string $v): string
    {
        return strtolower(trim((string) $v));
    }

    public static function resolveRoleKey(?string $role): string
    {
        $k = self::normText($role);

        return self::ALIAS_TO_KEY[$k] ?? $k;
    }

    public static function roleToCompat(string $roleKey): string
    {
        return self::KEY_TO_COMPAT[$roleKey] ?? $roleKey;
    }

    /** @param array<string>|null $explicitFromJwt */
    public static function getPermissions(?string $roleOrKey, ?array $explicitFromJwt): array
    {
        if (is_array($explicitFromJwt) && count($explicitFromJwt) > 0) {
            return array_values(array_unique($explicitFromJwt));
        }
        $key = self::resolveRoleKey($roleOrKey);

        return self::PERMISSIONS[$key] ?? [];
    }

    public static function hasPermission(object|array $userLike, string $permission): bool
    {
        $u = (array) $userLike;
        $explicit = isset($u['permissions']) && is_array($u['permissions']) ? $u['permissions'] : null;
        $roleKey = self::resolveRoleKey($u['roleKey'] ?? $u['role'] ?? '');
        $perms = self::getPermissions($roleKey, $explicit);

        return in_array($permission, $perms, true);
    }

    public static function isAdminKeuanganOrOwner(object|array $userLike): bool
    {
        $key = self::resolveRoleKey(is_array($userLike) ? ($userLike['roleKey'] ?? $userLike['role'] ?? '') : ($userLike->roleKey ?? $userLike->role ?? ''));

        return in_array($key, ['owner', 'admin_keuangan'], true);
    }

    public static function canViewFinanceTotals(object|array $userLike): bool
    {
        return self::hasPermission($userLike, 'view_finance_totals') || self::isAdminKeuanganOrOwner($userLike);
    }

    public static function canManageSettings(object|array $userLike): bool
    {
        return self::hasPermission($userLike, 'manage_settings')
            || self::hasPermission($userLike, 'manage_settings_wa')
            || self::isAdminKeuanganOrOwner($userLike);
    }

    public static function canViewIntegrationSecrets(object|array $userLike): bool
    {
        return self::hasPermission($userLike, 'manage_settings') || self::isAdminKeuanganOrOwner($userLike);
    }

    public static function isAdminRole(?string $role): bool
    {
        return self::hasPermission((object) ['role' => $role], 'access_admin_app');
    }

    public static function isOwnerUser(object|array $userLike): bool
    {
        $key = self::resolveRoleKey(is_array($userLike) ? ($userLike['roleKey'] ?? $userLike['role'] ?? '') : ($userLike->roleKey ?? $userLike->role ?? ''));
        $raw = self::normText(is_array($userLike) ? ($userLike['role'] ?? '') : ($userLike->role ?? ''));

        return $key === 'owner' || in_array($raw, ['owner', 'superadmin'], true);
    }
}
