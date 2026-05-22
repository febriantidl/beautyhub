<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Mua;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    // ── List semua user ───────────────────────────────────────────
    public function index(Request $request)
    {
        $query = User::withCount(['bookings'])
            ->with('mua');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
            );
        }

        $users = $query->orderByDesc('created_at')->paginate(20);

        $counts = [
            'all'      => User::count(),
            'admin'    => User::where('role','admin')->count(),
            'mua'      => User::where('role','mua')->count(),
            'customer' => User::where('role','customer')->count(),
        ];

        return view('admin.users.index', compact('users','counts'));
    }

    // ── Detail user ───────────────────────────────────────────────
    public function show(int $id)
    {
        $user = User::with('mua.services','mua.portfolios')->findOrFail($id);
        $bookings = Booking::with('mua.user','service')
            ->where('user_id', $id)
            ->orderByDesc('created_at')->limit(10)->get();

        return view('admin.users.show', compact('user','bookings'));
    }

    // ── Toggle aktif/nonaktif ─────────────────────────────────────
    public function toggleActive(int $id)
    {
        $user = User::findOrFail($id);
        if ($user->isAdmin()) {
            return back()->withErrors(['error' => 'Tidak bisa menonaktifkan admin.']);
        }
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    // ── Update role ───────────────────────────────────────────────
    public function updateRole(Request $request, int $id)
    {
        $request->validate(['role' => 'required|in:admin,mua,customer']);
        $user = User::findOrFail($id);
        $user->update(['role' => $request->role]);

        // Jika dijadikan MUA, pastikan punya profil mua
        if ($request->role === 'mua' && !$user->mua) {
            Mua::create(['user_id' => $user->id]);
        }

        return back()->with('success', "Role {$user->name} diubah ke {$request->role}.");
    }

    // ── Reset password ────────────────────────────────────────────
    public function resetPassword(int $id)
    {
        $user = User::findOrFail($id);
        $newPass = 'Password1!';
        $user->update(['password' => Hash::make($newPass)]);
        return back()->with('success', "Password {$user->name} direset ke: {$newPass}");
    }
}
