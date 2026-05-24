<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * POST /api/register
     */
    public function register(Request $request)
    {
        // 1. Validasi Fleksibel
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'mua_name' => 'nullable|string', // Cuma diisi kalau dia daftar sebagai MUA
        ]);

        if ($validator->fails()) {
            // Kalau request-nya dari API (Mobile), kirim JSON
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            // Kalau dari Web, redirect back
            return back()->withErrors($validator)->withInput();
        }

        $user = \DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->has('mua_name') ? 'mua' : 'customer',
            ]);

            // Kalau ada mua_name, otomatis bikinin profil MUA
            if ($request->has('mua_name')) {
                \App\Models\Mua::create([
                    'user_id' => $user->id,
                    'name'    => $request->mua_name,
                    'rating'  => 0,
                ]);
            }
            return $user;
        });

        // 2. Respon Berdasarkan Sumber
        if ($request->expectsJson()) {
            $token = $user->createToken('beautyhub_mobile_token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil.',
                'data'    => ['user' => $this->formatUser($user), 'access_token' => $token]
            ], 201);
        }

        \Auth::login($user);
        return redirect()->route('mua.dashboard')->with('success', 'Akun berhasil dibuat!');
    }


    /**
     * POST /api/login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda telah dinonaktifkan.',
            ], 403);
        }

        // Membuat Sanctum Token untuk di-save Nike di Shared Preferences Flutter
        $token = $user->createToken('beautyhub_mobile_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'user'         => $this->formatUser($user),
                'access_token' => $token,
                'token_type'   => 'bearer',
            ],
        ]);
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        // Hapus token sanctum aktif saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * GET /api/me
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $data = $this->formatUser($user);

        if ($user->role === 'mua' && $user->mua) {
            $data['mua_profile'] = $user->mua->load('services');
        }

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    private function formatUser(User $user): array
    {
        return [
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'role'      => $user->role,
            'phone'     => $user->phone,
            'avatar'    => $user->avatar ? asset('storage/' . $user->avatar) : null,
            'address'   => $user->address,
            'gender'    => $user->gender,
            'is_active' => $user->is_active,
        ];
    }
}