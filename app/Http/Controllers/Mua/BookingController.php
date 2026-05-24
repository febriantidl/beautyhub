<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // FIX ERROR: Call to undefined method index()
    public function index()
{
    $muaId = Auth::user()->mua->id;
    // Ganti ->get() jadi ->paginate(10) biar ada method total()
    $bookings = Booking::where('mua_id', $muaId)->orderBy('created_at', 'desc')->paginate(10);
    
    return view('mua.bookings.index', compact('bookings'));
}

    public function show($id)
    {
        $booking = Booking::findOrFail($id);
        return view('mua.bookings.show', compact('booking'));
    }

    public function approve($id)
    {
        Booking::where('id', $id)->update(['status' => 'approved']);
        return back()->with('success', 'Booking disetujui!');
    }

    public function reject($id)
    {
        Booking::where('id', $id)->update(['status' => 'rejected']);
        return back()->with('success', 'Booking ditolak!');
    }

    public function complete($id)
    {
        Booking::where('id', $id)->update(['status' => 'completed']);
        return back()->with('success', 'Booking selesai!');
    }

    public function verifyQrProcess(Request $request)
{
    $bookingId = $request->code; // Ini ID dari hasil scan QR
    $booking = Booking::find($bookingId);

    if ($booking && $booking->mua_id == Auth::user()->mua->id) {
        $booking->update(['status' => 'completed']);
        return response()->json(['success' => true, 'message' => 'Booking selesai!']);
    }

    return response()->json(['success' => false, 'message' => 'QR tidak valid!']);
}

public function checkAvailability(Request $request)
{
    $muaId = $request->mua_id;
    $date  = $request->event_date;

    // Anggap MUA cuma bisa handle 1-2 job per hari (atur sesuai keinginan lo)
    $maxCapacity = 3; 
    $bookedCount = Booking::where('mua_id', $muaId)
                          ->where('event_date', $date)
                          ->whereIn('status', ['pending', 'confirmed'])
                          ->count();

    return response()->json([
        'available' => $bookedCount < $maxCapacity,
        'message'   => $bookedCount < $maxCapacity ? 'Jadwal tersedia!' : 'Maaf, MUA penuh di tanggal ini.'
    ]);
}

    public function storeFromMobile(Request $request)
    {
        $validated = $request->validate([
            'mua_id'         => 'required|exists:muas,id',
            'customer_email' => 'required|email',
            'event_date'     => 'required|date',
            'service_id'     => 'required|exists:services,id',
            'location'       => 'required|string',
        ]);

        $user = User::where('email', $request->customer_email)->first();
        
        $booking = Booking::create([
            'mua_id'     => $request->mua_id,
            'user_id'    => $user ? $user->id : null,
            'service_id' => $request->service_id,
            'event_date' => $request->event_date,
            'location'   => $request->location,
            'status'     => 'pending', 
            'price'      => $request->price ?? 0,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Booking berhasil dibuat!',
            'booking_id' => $booking->id
        ], 201);
    }
}