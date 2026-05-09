<?php

namespace App\Http\Middleware;

use App\Services\StaffAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfStaffAuthenticated
{
    public function __construct(private StaffAuthService $staffAuth) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('staff_user')) {
            $path = $this->staffAuth->redirectPathForUser(session('staff_user'));

            return redirect()->to($path);
        }

        return $next($request);
    }
}
