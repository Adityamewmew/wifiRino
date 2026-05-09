<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Support\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::hasPermission($jwt, 'manage_backup_audit') && ! RoleHelper::isAdminKeuanganOrOwner($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $rows = DB::table('audit_logs')->orderByDesc('tanggal')->limit(500)->get()->map(fn ($r) => (array) $r)->all();

        return response()->json(['success' => true, 'data' => $rows]);
    }
}
