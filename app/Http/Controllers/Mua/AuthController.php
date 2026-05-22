<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Mua;
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

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        $user = Auth::user();

        if (!in_array($user->role, ['mua', 'admin'])) {
            Auth::logout();
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun ini tidak memiliki akses MUA Dashboard.']);
        }

        if (!$user->is_active) {
            Auth::logout();
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
        }

        // Pastikan MUA punya profil
        if ($user->isMua() && !$user->mua) {
            Mua::create(['user_id' => $user->id]);
        }

        $request->session()->regenerate();

        // Redirect berdasarkan role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('mua.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('mua.login');
    }
}
