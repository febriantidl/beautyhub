<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login atau belum
        if (!$request->user()) {
            return redirect()->route('mua.login');
        }

        // 2. Cek apakah role user saat ini ada di dalam daftar yang diizinkan
        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // 3. Kalau rolenya ga sesuai (misal customer nyasar), lempar error 403 unauthorized
        abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
    }
}