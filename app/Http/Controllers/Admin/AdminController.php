<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Mua;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalMua       = User::where('role', 'mua')->count();
        $totalCustomer  = User::where('role', 'customer')->count();
        $totalBookings  = Booking::count();
        $pendingBookings= Booking::where('status', 'pending')->count();
        $totalRevenue   = Booking::where('status', 'completed')->sum('price');
        $unverifiedMua  = Mua::where('is_verified', false)->count();

        $recentBookings = Booking::with(['customer', 'mua.user', 'service'])
            ->orderByDesc('created_at')->limit(8)->get();

        $topMuas = Mua::with('user')
            ->orderByDesc('rating')
            ->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalMua','totalCustomer','totalBookings',
            'pendingBookings','totalRevenue','unverifiedMua',
            'recentBookings','topMuas'
        ));
    }
}
