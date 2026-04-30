<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Mua;

class DashboardController extends Controller
{
    public function index()
    {
        $mua = Auth::user()->mua;
        if (!$mua) {
            // fallback jika user belum punya profil mua
            $mua = Mua::create(['user_id' => Auth::id()]);
        }

        $pendingCount = Booking::where('mua_id', $mua->id)->where('status', 'pending')->count();
        $portfolioCount = $mua->portfolios()->count();
        $rating = $mua->rating ?? 0;
        $totalRevenue = Booking::where('mua_id', $mua->id)->where('status', 'completed')->sum('price');

        $recentBookings = Booking::with('customer')
            ->where('mua_id', $mua->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('mua.dashboard', compact('pendingCount', 'portfolioCount', 'rating', 'totalRevenue', 'recentBookings'));
    }
}