<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
    $booking = Booking::findOrFail($id);

    $verificationCode = strtoupper(
        Str::random(8)
    );

    $qrContent = json_encode([
        'booking_id' => $booking->id,
        'code' => $verificationCode,
    ]);

    $fileName = 'booking_'.$booking->id.'.svg';

$filePath = 'qrcodes/'.$fileName;

Storage::disk('public')->put(
    $filePath,
    QrCode::format('svg')
        ->size(300)
        ->generate($qrContent)
);

    $booking->update([
        'status' => 'approved',
        'verification_code' => $verificationCode,
        'qr_code_path' => $filePath,
    ]);

    return back()->with(
        'success',
        'Booking disetujui dan QR berhasil dibuat'
    );
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
        'time_slot' => 'required|string',
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

    $slotExists = Booking::where('mua_id', $request->mua_id)
    ->where('event_date', $request->event_date)
    ->where('time_slot', $request->time_slot)
    ->whereIn('status', [
        'pending',
        'approved',
        'verified'
    ])
    ->exists();

if ($slotExists) {
    return response()->json([
        'success' => false,
        'message' => 'Slot sudah terisi'
    ], 422);
}

    $booking = Booking::create([

    'booking_code' => 'BK-'.strtoupper(substr(md5(uniqid()),0,8)),

    'user_id' => $user->id,

    'mua_id' => $request->mua_id,

    'service_id' => $request->service_id,

    'booking_date' => now()->toDateString(),

    'event_date' => $request->event_date,
    'time_slot' => $request->time_slot,

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