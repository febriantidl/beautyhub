<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Periksa apakah user yang login memiliki salah satu role yang diizinkan.
     * Usage: ->middleware('role:mua,admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('mua.login');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Insufficient role.'], 403);
            }
            Auth::logout();
            return redirect()->route('mua.login')
                ->withErrors(['email' => 'Anda tidak memiliki akses ke halaman ini.']);
        }

        // Cek apakah akun aktif
        if (!$user->is_active) {
            Auth::logout();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun Anda dinonaktifkan.'], 403);
            }
            return redirect()->route('mua.login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
        }

        return $next($request);
    }
}
