<?php

namespace App\Support;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class JwtHelper
{
    public static function secret(): string
    {
        return (string) config('billing.jwt_secret');
    }

    /** @return object{uid?:string,email?:string,role?:string,roleKey?:string,permissions?:array}|null */
    public static function tryDecode(Request $request): ?object
    {
        $auth = $request->header('Authorization', '');
        if (! str_starts_with($auth, 'Bearer ')) {
            return null;
        }
        $token = trim(substr($auth, 7));
        if ($token === '') {
            return null;
        }
        try {
            return JWT::decode($token, new Key(self::secret(), 'HS256'));
        } catch (\Throwable) {
            return null;
        }
    }
}
