<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $mua = Auth::user()->mua;

        if (!$mua) {
            return back()->with('error', 'Akun ini belum terhubung dengan data MUA.');
        }

        $muaId = $mua->id;

        $query = Booking::with(['user', 'service'])
            ->where('mua_id', $muaId);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(10);

        $counts = (object) [
            'pending'   => Booking::where('mua_id', $muaId)->where('status', Booking::STATUS_PENDING)->count(),
            'confirmed' => Booking::where('mua_id', $muaId)->where('status', Booking::STATUS_APPROVED)->count(),
            'verified'  => Booking::where('mua_id', $muaId)->where('status', Booking::STATUS_VERIFIED)->count(),
            'completed' => Booking::where('mua_id', $muaId)->where('status', Booking::STATUS_COMPLETED)->count(),
        ];

        return view('mua.bookings.index', compact('bookings', 'counts'));
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'mua', 'service'])->findOrFail($id);

        if (Auth::user()->mua && $booking->mua_id !== Auth::user()->mua->id) {
            abort(403, 'Booking bukan milik Anda.');
        }

        return view('mua.bookings.show', compact('booking'));
    }

    public function approve($id)
    {
        $booking = Booking::with(['user', 'mua'])->findOrFail($id);

        if (Auth::user()->mua && $booking->mua_id !== Auth::user()->mua->id) {
            abort(403, 'Booking bukan milik Anda.');
        }

        $verificationCode = $booking->verification_code;

        if (!$verificationCode) {
            $verificationCode = $this->generateUniqueVerificationCode();
        }

        /*
         * QR CODE TIDAK DIBUAT DI LARAVEL LAGI.
         * Laravel hanya membuat verification_code.
         * Mobile/Flutter yang menampilkan QR berdasarkan verification_code.
         */

        $booking->update([
            'status'            => Booking::STATUS_APPROVED,
            'verification_code' => $verificationCode,
            'qr_code_path'      => null,
            'verified'          => false,
            'verified_at'       => null,
            'rejection_reason'  => null,
        ]);

        if ($booking->user) {
            $booking->user->notify(
                new BookingStatusNotification($booking, Booking::STATUS_APPROVED)
            );
        }

        return back()->with('success', 'Booking disetujui. Kode verifikasi berhasil dibuat.');
    }

    public function reject($id)
    {
        $booking = Booking::with(['user', 'mua'])->findOrFail($id);

        if (Auth::user()->mua && $booking->mua_id !== Auth::user()->mua->id) {
            abort(403, 'Booking bukan milik Anda.');
        }

        $booking->update([
            'status'           => Booking::STATUS_REJECTED,
            'rejection_reason' => request('rejection_reason'),
            'verified'         => false,
            'verified_at'      => null,
        ]);

        if ($booking->user) {
            $booking->user->notify(
                new BookingStatusNotification($booking, Booking::STATUS_REJECTED)
            );
        }

        return back()->with('success', 'Booking ditolak.');
    }

    public function complete($id)
    {
        $booking = Booking::findOrFail($id);

        if (Auth::user()->mua && $booking->mua_id !== Auth::user()->mua->id) {
            abort(403, 'Booking bukan milik Anda.');
        }

        $booking->update([
            'status' => Booking::STATUS_COMPLETED,
        ]);

        return back()->with('success', 'Booking selesai.');
    }

    public function verifyQr(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string',
        ]);

        $rawCode = trim($request->verification_code);

        /*
         * Mendukung dua format:
         * 1. QR baru dari mobile: BNJNJJDB
         * 2. QR lama hasil testing Laravel: {"booking_id":16,"code":"BNJNJJDB"}
         */
        $decoded = json_decode($rawCode, true);

        if (is_array($decoded) && isset($decoded['code'])) {
            $code = trim($decoded['code']);
        } else {
            $code = $rawCode;
        }

        $code = strtoupper($code);

        $booking = Booking::where('verification_code', $code)->first();

        if (!$booking) {
            return back()->with('error', 'Kode verifikasi tidak ditemukan.');
        }

        $mua = Auth::user()->mua;

        if (!$mua) {
            return back()->with('error', 'Akun ini belum terhubung dengan data MUA.');
        }

        if ($booking->mua_id !== $mua->id) {
            return back()->with('error', 'Booking bukan milik Anda.');
        }

        if ($booking->verified || $booking->status === Booking::STATUS_VERIFIED) {
            return back()->with('error', 'Booking sudah pernah diverifikasi.');
        }

        if ($booking->status !== Booking::STATUS_APPROVED) {
            return back()->with('error', 'Booking belum disetujui atau tidak bisa diverifikasi.');
        }

        $booking->update([
            'status'      => Booking::STATUS_VERIFIED,
            'verified'    => true,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Booking berhasil diverifikasi.');
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'mua_id'     => 'required|exists:muas,id',
            'event_date' => 'required|date',
        ]);

        $muaId       = $request->mua_id;
        $date        = $request->event_date;
        $maxCapacity = 3;

        $bookedCount = Booking::where('mua_id', $muaId)
            ->where('event_date', $date)
            ->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_APPROVED,
                Booking::STATUS_VERIFIED,
            ])
            ->count();

        return response()->json([
            'available' => $bookedCount < $maxCapacity,
            'message'   => $bookedCount < $maxCapacity
                ? 'Jadwal tersedia.'
                : 'Maaf, MUA penuh di tanggal ini.',
        ]);
    }

    public function storeFromMobile(Request $request)
    {
        $validated = $request->validate([
            'mua_id'           => 'required|exists:muas,id',
            'service_id'       => 'nullable|exists:services,id',
            'customer_email'   => 'nullable|email',
            'event_date'       => 'required|date',
            'time_slot'        => 'required|string|max:20',
            'location'         => 'nullable|string',
            'location_address' => 'nullable|string',
            'location_notes'   => 'nullable|string',
            'price'            => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string',
            'reference_image'  => 'nullable|string',
        ]);

        if (Auth::check()) {
            $user = Auth::user();
        } elseif ($request->filled('customer_email')) {
            $user = User::where('email', $request->customer_email)->first();
        } else {
            $user = null;
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User customer tidak ditemukan.',
            ], 404);
        }

        $locationAddress = $request->location_address ?? $request->location;

        if (!$locationAddress) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat lokasi wajib diisi.',
            ], 422);
        }

        $slotExists = Booking::where('mua_id', $request->mua_id)
            ->where('event_date', $request->event_date)
            ->where('time_slot', $request->time_slot)
            ->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_APPROVED,
                Booking::STATUS_VERIFIED,
            ])
            ->exists();

        if ($slotExists) {
            return response()->json([
                'success' => false,
                'message' => 'Slot sudah terisi.',
            ], 422);
        }

        $bookingCode = $this->generateUniqueBookingCode();

        $booking = Booking::create([
            'booking_code'     => $bookingCode,
            'user_id'          => $user->id,
            'mua_id'           => $request->mua_id,
            'service_id'       => $request->service_id,
            'booking_date'     => now()->toDateString(),
            'event_date'       => $request->event_date,
            'time_slot'        => $request->time_slot,
            'location_address' => $locationAddress,
            'location_notes'   => $request->location_notes,
            'price'            => $request->price ?? 0,
            'notes'            => $request->notes,
            'reference_image'  => $request->reference_image,
            'status'           => Booking::STATUS_PENDING,
            'verified'         => false,
            'verified_at'      => null,
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Booking berhasil dibuat.',
            'booking_id'   => $booking->id,
            'booking_code' => $booking->booking_code,
        ], 201);
    }

    private function generateUniqueVerificationCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Booking::where('verification_code', $code)->exists());

        return $code;
    }

    private function generateUniqueBookingCode(): string
    {
        do {
            $code = 'BK-' . strtoupper(substr(md5(uniqid('', true)), 0, 8));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }
}