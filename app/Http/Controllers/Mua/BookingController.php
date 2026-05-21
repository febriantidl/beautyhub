<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * GET /mua/bookings
     * Daftar semua booking untuk MUA yang sedang login.
     */
    public function index(Request $request)
    {
        $mua = Auth::user()->mua;

        if (!$mua) {
            return redirect()->route('mua.dashboard')
                ->withErrors(['error' => 'Profil MUA tidak ditemukan.']);
        }

        $query = Booking::with(['customer', 'service'])
            ->where('mua_id', $mua->id);

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderByDesc('created_at')->paginate(15);

        $counts = [
            'pending'   => Booking::where('mua_id', $mua->id)->where('status', 'pending')->count(),
            'approved'  => Booking::where('mua_id', $mua->id)->where('status', 'approved')->count(),
            'completed' => Booking::where('mua_id', $mua->id)->where('status', 'completed')->count(),
        ];

        return view('mua.bookings.index', compact('bookings', 'counts'));
    }

    /**
     * GET /mua/bookings/{id}
     */
    public function show(int $id)
    {
        $mua     = Auth::user()->mua;
        $booking = Booking::with(['customer', 'service', 'review'])
            ->where('mua_id', $mua->id)
            ->findOrFail($id);

        return view('mua.bookings.show', compact('booking'));
    }

    /**
     * POST /mua/bookings/{id}/approve
     */
    public function approve(Request $request, int $id)
    {
        $mua     = Auth::user()->mua;
        $booking = Booking::where('mua_id', $mua->id)
            ->where('status', Booking::STATUS_PENDING)
            ->findOrFail($id);

        $booking->update(['status' => Booking::STATUS_APPROVED]);

        return redirect()->back()
            ->with('success', "Booking #{$booking->id} berhasil disetujui.");
    }

    /**
     * POST /mua/bookings/{id}/reject
     */
    public function reject(Request $request, int $id)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:300',
        ]);

        $mua     = Auth::user()->mua;
        $booking = Booking::where('mua_id', $mua->id)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_APPROVED])
            ->findOrFail($id);

        $booking->update([
            'status'           => Booking::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason ?? 'Tidak tersedia.',
        ]);

        return redirect()->back()
            ->with('success', "Booking #{$booking->id} telah ditolak.");
    }

    /**
     * POST /mua/bookings/{id}/complete
     */
    public function complete(int $id)
    {
        $mua     = Auth::user()->mua;
        $booking = Booking::where('mua_id', $mua->id)
            ->where('status', Booking::STATUS_VERIFIED)
            ->findOrFail($id);

        $booking->update(['status' => Booking::STATUS_COMPLETED]);

        return redirect()->back()
            ->with('success', "Booking #{$booking->id} ditandai selesai.");
    }

    /**
     * POST /mua/bookings/verify-qr
     * Verifikasi kode QR dari customer.
     */
    public function verifyQr(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:8',
        ]);

        $mua     = Auth::user()->mua;
        $booking = Booking::with('customer')
            ->where('mua_id', $mua->id)
            ->where('verification_code', strtoupper($request->verification_code))
            ->where('status', Booking::STATUS_APPROVED)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi tidak valid atau booking tidak ditemukan.',
            ], 404);
        }

        $booking->update([
            'status'      => Booking::STATUS_VERIFIED,
            'verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi berhasil!',
            'data'    => [
                'booking_id'   => $booking->id,
                'customer'     => $booking->customer->name,
                'event_date'   => $booking->event_date->format('d M Y'),
                'time_slot'    => $booking->time_slot,
                'verified_at'  => now()->format('d M Y H:i'),
            ],
        ]);
    }
}
