<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
{
    $mua = Auth::user()->mua;
    if (!$mua) return redirect()->route('mua.profile');

    $pendingCount   = Booking::where('mua_id', $mua->id)->where('status', 'pending')->count();
    $recentBookings = Booking::with('customer', 'service')->where('mua_id', $mua->id)->orderByDesc('created_at')->limit(5)->get();
    
    // Data buat Kalender (ambil yang confirmed)
    // Ganti query $bookings lo jadi kayak gini:
$bookings = Booking::where('mua_id', $mua->id)
                   ->where('status', 'confirmed')
                   ->get();

    return view('mua.dashboard', compact('pendingCount', 'recentBookings', 'bookings'));
}
}