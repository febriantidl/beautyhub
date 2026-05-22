<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('mua.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Coba login
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        $user = Auth::user();

        // Pastikan role mua atau admin
        if (!in_array($user->role, ['mua', 'admin'])) {
            Auth::logout();
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun ini tidak memiliki akses MUA Dashboard.']);
        }

        // Pastikan akun aktif
        if (!$user->is_active) {
            Auth::logout();
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
        }

        // Pastikan punya profil MUA, buat jika belum ada
        if ($user->isMua() && !$user->mua) {
            \App\Models\Mua::create(['user_id' => $user->id]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('mua.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('mua.login');
    }
}
