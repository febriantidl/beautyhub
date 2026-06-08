<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function index()
    {
        $mua = Auth::user()->mua;

        // Booking yang baru diverifikasi (hari ini)
        $recentVerifications = $mua ? Booking::with('customer', 'service')
            ->where('mua_id', $mua->id)
            ->where('status', 'verified')
            ->whereDate('verified_at', today())
            ->orderByDesc('verified_at')
            ->get() : collect();

        return view('mua.verification', compact('recentVerifications'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|min:6|max:10',
        ]);

        $mua  = Auth::user()->mua;
        $code = strtoupper(trim($request->verification_code));

        $booking = Booking::with(['customer', 'service'])
            ->where('mua_id', $mua->id)
            ->where('verification_code', $code)
            ->where('status', 'approved')
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Kode tidak valid atau booking sudah pernah diverifikasi.',
            ], 404);
        }

        $booking->update([
            'status'      => Booking::STATUS_VERIFIED,
            'verified'    => true,
            'verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Verifikasi berhasil! Pelanggan dapat dilayani.',
            'data'    => [
                'booking_id'   => $booking->id,
                'customer'     => [
                    'name'   => $booking->customer->name,
                    'phone'  => $booking->customer->phone,
                    'avatar' => $booking->customer->avatar
                        ? asset('storage/' . $booking->customer->avatar) : null,
                ],
                'service'      => $booking->service?->name ?? 'Custom',
                'event_date'   => $booking->event_date->format('d M Y'),
                'time_slot'    => $booking->time_slot ?? '-',
                'location'     => $booking->location_address,
                'price'        => 'Rp ' . number_format($booking->price, 0, ',', '.'),
                'verified_at'  => now()->format('d M Y, H:i'),
            ],
        ]);
    }
}
