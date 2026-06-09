<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * POST /api/register
     */
    public function register(Request $request)
    {
        try {

            Log::info('Register request masuk', [
                'email' => $request->email,
                'name' => $request->name,
            ]);

            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:100',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'mua_name' => 'nullable|string',
            ]);

            if ($validator->fails()) {

                Log::warning('Validasi register gagal', [
                    'errors' => $validator->errors()->toArray(),
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors(),
                    ], 422);
                }

                return back()->withErrors($validator)->withInput();
            }

            $user = \DB::transaction(function () use ($request) {

                Log::info('Membuat user baru');

                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password),
                    'role'     => $request->has('mua_name') ? 'mua' : 'customer',
                ]);

                Log::info('User berhasil dibuat', [
                    'user_id' => $user->id,
                ]);

                if ($request->has('mua_name')) {

                    Log::info('Membuat profil MUA', [
                        'user_id' => $user->id,
                        'mua_name' => $request->mua_name,
                    ]);

                    \App\Models\Mua::create([
                        'user_id' => $user->id,
                        'name'    => $request->mua_name,
                        'rating'  => 0,
                    ]);

                    Log::info('Profil MUA berhasil dibuat', [
                        'user_id' => $user->id,
                    ]);
                }

                return $user;
            });

            Log::info('Generate Sanctum Token', [
                'user_id' => $user->id,
            ]);

            if ($request->expectsJson()) {

                $token = $user->createToken('beautyhub_mobile_token')->plainTextToken;

                Log::info('Token berhasil dibuat', [
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Registrasi berhasil.',
                    'data'    => [
                        'user' => [
                            'id'    => $user->id,
                            'name'  => $user->name,
                            'email' => $user->email,
                            'role'  => $user->role,
                        ],
                        'access_token' => $token,
                        'token_type'   => 'bearer',
                    ]
                ], 201);
            }

            Log::info('Login otomatis setelah register', [
                'user_id' => $user->id,
            ]);

            Auth::login($user);

            return redirect()
                ->route('mua.dashboard')
                ->with('success', 'Akun berhasil dibuat!');

        } catch (\Exception $e) {

            Log::error('Register error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat registrasi.',
            ], 500);
        }
    }

    /**
     * POST /api/login
     */
    public function login(Request $request)
    {
        try {

            Log::info('Login request masuk', [
                'email' => $request->email,
            ]);

            $validator = Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {

                Log::warning('Validasi login gagal', [
                    'errors' => $validator->errors()->toArray(),
                ]);

                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            if (!Auth::attempt($request->only('email', 'password'))) {

                Log::warning('Login gagal', [
                    'email' => $request->email,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah.',
                ], 401);
            }

            $user = Auth::user();

            Log::info('User ditemukan', [
                'user_id' => $user->id,
                'role' => $user->role,
            ]);

            if (!$user->is_active) {

                Log::warning('Akun tidak aktif', [
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda telah dinonaktifkan.',
                ], 403);
            }

            Log::info('Membuat token login', [
                'user_id' => $user->id,
            ]);

            $token = $user->createToken('beautyhub_mobile_token')->plainTextToken;

            Log::info('Login berhasil', [
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'data'    => [
                    'user'         => $this->formatUser($user),
                    'access_token' => $token,
                    'token_type'   => 'bearer',
                ],
            ]);

        } catch (\Exception $e) {

            Log::error('Login error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server.',
            ], 500);
        }
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        try {

            Log::info('Logout request', [
                'user_id' => $request->user()->id,
            ]);

            $request->user()->currentAccessToken()->delete();

            Log::info('Logout berhasil', [
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil.',
            ]);

        } catch (\Exception $e) {

            Log::error('Logout error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat logout.',
            ], 500);
        }
    }

    /**
     * GET /api/me
     */
    public function me(Request $request)
    {
        try {

            $user = $request->user();

            Log::info('Get profile user', [
                'user_id' => $user->id,
            ]);

            $data = $this->formatUser($user);

            if ($user->role === 'mua' && $user->mua) {

                Log::info('Load profil MUA', [
                    'user_id' => $user->id,
                ]);

                $data['mua_profile'] = $user->mua->load('services');
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);

        } catch (\Exception $e) {

            Log::error('Me endpoint error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data user.',
            ], 500);
        }
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