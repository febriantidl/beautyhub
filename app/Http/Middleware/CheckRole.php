<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
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
                return response()->json(['message' => 'Forbidden.'], 403);
            }
            // Redirect ke dashboard yang sesuai rolenya
            if ($user->isAdmin()) return redirect()->route('admin.dashboard');
            if ($user->isMua())   return redirect()->route('mua.dashboard');
            Auth::logout();
            return redirect()->route('mua.login')
                ->withErrors(['email' => 'Akses ditolak.']);
        }

        if (!$user->is_active) {
            Auth::logout();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun dinonaktifkan.'], 403);
            }
            return redirect()->route('mua.login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
        }

        return $next($request);
    }
}
