<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Endpoint yang belum di-port dari server Node. Daftar & prioritas: lihat MIGRATION_STATUS.md
 */
class NotImplementedController extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->json([
            'error' => 'Endpoint belum tersedia di Laravel (lihat dokumentasi pengembangan API).',
            'method' => $request->method(),
            'path' => '/'.$request->path(),
        ], 501);
    }
}
