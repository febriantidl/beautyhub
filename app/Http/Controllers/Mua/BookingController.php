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
    public function index(Request $request)
{
    $muaId = Auth::user()->mua->id;

    $query = Booking::where('mua_id', $muaId);

    if (
        $request->status &&
        $request->status !== 'all'
    ) {
        $query->where(
            'status',
            $request->status
        );
    }

    $bookings = $query
        ->latest()
        ->paginate(10);

    $counts = (object)[
        'pending' => Booking::where('mua_id',$muaId)
            ->where('status','pending')
            ->count(),

        'confirmed' => Booking::where('mua_id',$muaId)
            ->where('status','approved')
            ->count(),

        'completed' => Booking::where('mua_id',$muaId)
            ->where('status','completed')
            ->count(),
    ];

    return view(
        'mua.bookings.index',
        compact('bookings','counts')
    );
}

    public function show($id)
    {
        $booking = Booking::findOrFail($id);
        return view('mua.bookings.show', compact('booking'));
    }

    public function approve($id)
{
    Booking::where('id', $id)
        ->update([
            'status' => 'approved'
        ]);

    return back()->with('success', 'Booking disetujui');
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

    public function verifyQr(Request $request)
{
    $booking = Booking::where(
        'verification_code',
        $request->verification_code
    )->first();

    if (!$booking) {
        return back()->with(
            'error',
            'Kode verifikasi tidak ditemukan'
        );
    }

    if ($booking->mua_id != Auth::user()->mua->id) {
        return back()->with(
            'error',
            'Booking bukan milik Anda'
        );
    }

    $booking->update([
        'status' => 'verified',
        'verified_at' => now()
    ]);

    return back()->with(
        'success',
        'Booking berhasil diverifikasi'
    );
}

public function checkAvailability(Request $request)
{
    $muaId = $request->mua_id;
    $date  = $request->event_date;

    // Anggap MUA cuma bisa handle 1-2 job per hari (atur sesuai keinginan lo)
    $maxCapacity = 3; 
    $bookedCount = Booking::where('mua_id', $muaId)
                          ->where('event_date', $date)
                          ->whereIn('status', [
    'pending',
    'approved',
    'verified'
])
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

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User tidak ditemukan'
        ], 404);
    }

    $booking = Booking::create([

    'booking_code' => 'BK-'.strtoupper(substr(md5(uniqid()),0,8)),

    'user_id' => $user->id,

    'mua_id' => $request->mua_id,

    'service_id' => $request->service_id,

    'booking_date' => now()->toDateString(),

    'event_date' => $request->event_date,

    'location_address' => $request->location,

    'price' => $request->price ?? 0,

    'status' => 'pending',

]);

    return response()->json([
        'success' => true,
        'message' => 'Booking berhasil dibuat',
        'booking_id' => $booking->id
    ], 201);
}
}