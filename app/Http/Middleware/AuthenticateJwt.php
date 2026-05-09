<?php

namespace App\Http\Middleware;

use App\Support\JwtHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        $decoded = JwtHelper::tryDecode($request);
        if ($decoded === null) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $arr = json_decode(json_encode($decoded, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        $request->attributes->set('jwt_user', $arr);

        return $next($request);
    }
}
