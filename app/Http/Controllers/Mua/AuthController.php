<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Mua;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm() { 
        return Auth::check() ? redirect()->route('mua.dashboard') : view('mua.login'); 
    }

    public function showRegisterForm() { 
        return view('auth.register'); 
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required', 'email' => 'required|email|unique:users',
            'password' => 'required|min:3', 'mua_name' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name, 'email' => $request->email,
            'password' => Hash::make($request->password), 'role' => 'mua'
        ]);

        Mua::create(['user_id' => $user->id, 'name' => $request->mua_name, 'rating' => 0]);

        return redirect()->route('mua.login')->with('success', 'Berhasil daftar!');
    }

    public function login(Request $request) {
        $creds = $request->validate(['email' => 'required|email', 'password' => 'required']);
        
        // INGAT: Pakai false biar nggak error remember_token di database
        if (Auth::attempt($creds, false)) {
            $request->session()->regenerate();
            return redirect()->intended('/mua/dashboard');
        }
        return back()->withErrors(['email' => 'Login gagal!']);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        return redirect()->route('mua.login');
    }
}