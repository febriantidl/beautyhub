<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Mua;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mua  = $user->mua;

        if (!$mua) {
            $mua = Mua::create(['user_id' => $user->id]);
        }

        $pendingCount    = Booking::where('mua_id', $mua->id)->where('status', 'pending')->count();
        $portfolioCount  = $mua->portfolios()->count();
        $rating          = $mua->rating ?? 0;
        $totalRevenue    = Booking::where('mua_id', $mua->id)
                            ->where('status', 'completed')
                            ->sum('price');

        $recentBookings  = Booking::with('customer', 'service')
                            ->where('mua_id', $mua->id)
                            ->orderByDesc('created_at')
                            ->limit(6)
                            ->get();

        return view('mua.dashboard', compact(
            'pendingCount', 'portfolioCount', 'rating', 'totalRevenue', 'recentBookings'
        ));
    }
}
