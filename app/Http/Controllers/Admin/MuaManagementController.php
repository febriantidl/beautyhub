<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Mua;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MuaManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Mua::with('user')
            ->withCount(['bookings','portfolios','services']);

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) =>
                $q->where('name','like','%'.$request->search.'%')
                  ->orWhere('email','like','%'.$request->search.'%')
            );
        }
        if ($request->filled('verified')) {
            $query->where('is_verified', $request->verified === '1');
        }

        $muas = $query->orderByDesc('rating')->paginate(15);

        return view('admin.muas.index', compact('muas'));
    }

    public function show(int $id)
    {
        $mua = Mua::with('user','services','portfolios')->findOrFail($id);
        $bookings = Booking::with('customer','service')
            ->where('mua_id', $id)->orderByDesc('created_at')->limit(10)->get();
        $revenue = Booking::where('mua_id', $id)->where('status','completed')->sum('price');

        return view('admin.muas.show', compact('mua','bookings','revenue'));
    }

    public function toggleVerified(int $id)
    {
        $mua = Mua::findOrFail($id);
        $mua->update(['is_verified' => !$mua->is_verified]);
        $status = $mua->is_verified ? 'diverifikasi' : 'dibatalkan verifikasinya';
        return back()->with('success', "MUA {$mua->user->name} berhasil {$status}.");
    }

    // Buat akun MUA baru dari admin
    public function store(Request $request)
{
    $request->validate([
        'name'         => 'required|string|max:100',
        'email'        => 'required|email|unique:users,email',
        'phone'        => 'nullable|string|max:20',
        'location'     => 'nullable|string|max:100',
        'password'     => 'required|string|min:8',
        'spesialisasi' => 'required|string', // Tambahan biar sinkron
        'harga'        => 'required|numeric', // Tambahan buat patokan harga awal
    ]);

    $user = User::create([
        'name'         => $request->name,
        'email'        => $request->email,
        'phone_number' => $request->phone, // Sesuaikan dengan kolom database asli (phone_number)
        'password'     => Hash::make($request->password),
        'role'         => 'mua',
        'is_active'    => true,
    ]);

    Mua::create([
        'user_id'      => $user->id,
        'nama_mua'     => $request->name,
        'spesialisasi' => $request->spesialisasi,
        'harga'        => $request->harga,
        'lokasi'       => $request->location,
        'is_verified'  => true, // Otomatis aktif karena dibuat langsung oleh Admin
        'rating'       => 5.0,  // Rating awal default
    ]);

    return redirect()->route('admin.muas.index')
        ->with('success', "Akun MUA {$user->name} berhasil dibuat beserta profilnya.");
}
}
