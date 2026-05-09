<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Support\JwtHelper;
use App\Support\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PushRegisterController extends Controller
{
    public function register(Request $request)
    {
        $token = trim((string) $request->input('token', ''));
        if ($token === '' || strlen($token) < 20) {
            return response()->json(['error' => 'Token FCM tidak valid'], 400);
        }
        $platform = strtolower(trim((string) $request->input('platform', 'android')));
        $deviceInfo = $request->input('deviceInfo');
        $deviceInfoStr = is_array($deviceInfo) || is_object($deviceInfo) ? json_encode($deviceInfo) : null;

        $authUser = JwtHelper::tryDecode($request);
        $reqTargetType = strtolower(trim((string) $request->input('targetType', '')));
        $reqTargetId = trim((string) $request->input('targetId', ''));
        $reqRoleKey = strtolower(trim((string) $request->input('roleKey', '')));

        $targetType = 'global';
        $targetId = null;
        $roleKey = null;
        if ($authUser && ! empty($authUser->uid)) {
            $targetType = 'user';
            $targetId = $authUser->uid;
            $roleKey = RoleHelper::resolveRoleKey($authUser->roleKey ?? $authUser->role ?? $reqRoleKey);
        } elseif ($reqTargetType !== '' && $reqTargetId !== '') {
            $targetType = $reqTargetType;
            $targetId = $reqTargetId;
            $roleKey = $reqRoleKey !== '' ? RoleHelper::resolveRoleKey($reqRoleKey) : null;
        }

        $now = now()->toIso8601String();
        $existing = DB::table('push_tokens')->where('token', $token)->first();
        if ($existing) {
            DB::table('push_tokens')->where('id', $existing->id)->update([
                'targetType' => $targetType,
                'targetId' => $targetId,
                'roleKey' => $roleKey,
                'platform' => $platform,
                'deviceInfo' => $deviceInfoStr,
                'isActive' => 1,
                'lastSeen' => $now,
                'updatedAt' => $now,
            ]);
        } else {
            DB::table('push_tokens')->insert([
                'id' => (string) Str::uuid(),
                'token' => $token,
                'targetType' => $targetType,
                'targetId' => $targetId,
                'roleKey' => $roleKey,
                'platform' => $platform,
                'deviceInfo' => $deviceInfoStr,
                'isActive' => 1,
                'lastSeen' => $now,
                'createdAt' => $now,
                'updatedAt' => $now,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
