<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('staff_user')) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai staf.');
        }

        return $next($request);
    }
}
