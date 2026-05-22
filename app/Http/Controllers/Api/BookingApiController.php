<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class BookingApiController extends Controller
{
    /**
     * POST /api/bookings
     */
    public function store(Request $request)
    {
        $user = JWTAuth::user();

        $validator = Validator::make($request->all(), [
            'mua_id'           => 'required|exists:muas,id',
            'service_id'       => 'nullable|exists:services,id',
            'event_date'       => 'required|date|after:today',
            'time_slot'        => 'nullable|string|max:20',
            'location_address' => 'required|string|max:500',
            'location_notes'   => 'nullable|string|max:300',
            'notes'            => 'nullable|string|max:500',
            'reference_image'  => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $price = 0;
        if ($request->filled('service_id')) {
            $service = Service::find($request->service_id);
            if ($service && $service->mua_id == $request->mua_id) {
                $price = $service->price;
            }
        }

        $imagePath = null;
        if ($request->hasFile('reference_image')) {
            $imagePath = $request->file('reference_image')->store('reference_images', 'public');
        }

        // Generate unique verification code
        do {
            $verificationCode = strtoupper(Str::random(8));
        } while (Booking::where('verification_code', $verificationCode)->exists());

        $booking = Booking::create([
            'user_id'          => $user->id,
            'mua_id'           => $request->mua_id,
            'service_id'       => $request->service_id,
            'booking_date'     => now()->toDateString(),
            'event_date'       => $request->event_date,
            'time_slot'        => $request->time_slot,
            'location_address' => $request->location_address,
            'location_notes'   => $request->location_notes,
            'price'            => $price,
            'notes'            => $request->notes,
            'reference_image'  => $imagePath,
            'status'           => Booking::STATUS_PENDING,
            'verification_code'=> $verificationCode,
        ]);

        // Generate QR code saat booking diapprove (lihat approveBooking)
        // Di sini cukup simpan booking dulu

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibuat. Menunggu konfirmasi MUA.',
            'data'    => $this->formatBooking($booking->load(['mua.user', 'service'])),
        ], 201);
    }

    /**
     * GET /api/bookings/my
     */
    public function myBookings(Request $request)
    {
        $user  = JWTAuth::user();
        $query = Booking::with(['mua.user', 'service', 'review'])
            ->where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderByDesc('created_at')->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $bookings->map(fn($b) => $this->formatBooking($b)),
            'meta'    => [
                'current_page' => $bookings->currentPage(),
                'last_page'    => $bookings->lastPage(),
                'total'        => $bookings->total(),
            ],
        ]);
    }

    /**
     * GET /api/bookings/{id}
     */
    public function show(int $id)
    {
        $user    = JWTAuth::user();
        $booking = Booking::with(['mua.user', 'service', 'review'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $this->formatBooking($booking),
        ]);
    }

    /**
     * PUT /api/bookings/{id}/cancel
     */
    public function cancel(int $id)
    {
        $user    = JWTAuth::user();
        $booking = Booking::where('user_id', $user->id)->findOrFail($id);

        if (!in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_APPROVED])) {
            return response()->json(['success' => false, 'message' => 'Booking tidak dapat dibatalkan.'], 422);
        }

        $booking->update(['status' => Booking::STATUS_CANCELLED]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibatalkan.',
            'data'    => $this->formatBooking($booking->fresh(['mua.user', 'service'])),
        ]);
    }

    // ── Private Helper ────────────────────────────────────────────
    private function formatBooking(Booking $booking): array
    {
        $qrCodeUrl = null;
        if ($booking->status === Booking::STATUS_APPROVED) {
            // Generate QR jika belum ada
            if (!$booking->qr_code_path) {
                $qrCodeUrl = $this->generateQrCode($booking);
            } else {
                $qrCodeUrl = asset('storage/' . $booking->qr_code_path);
            }
        }

        return [
            'id'               => $booking->id,
            'mua'              => $booking->mua ? [
                'id'     => $booking->mua->id,
                'name'   => $booking->mua->user->name ?? '-',
                'avatar' => $booking->mua->user->avatar ? asset('storage/' . $booking->mua->user->avatar) : null,
            ] : null,
            'service'          => $booking->service ? [
                'id'    => $booking->service->id,
                'name'  => $booking->service->name,
                'price' => $booking->service->price,
            ] : null,
            'booking_date'     => $booking->booking_date?->toDateString(),
            'event_date'       => $booking->event_date?->toDateString(),
            'time_slot'        => $booking->time_slot,
            'location_address' => $booking->location_address,
            'location_notes'   => $booking->location_notes,
            'price'            => $booking->price,
            'notes'            => $booking->notes,
            'reference_image'  => $booking->reference_image ? asset('storage/' . $booking->reference_image) : null,
            'status'           => $booking->status,
            'verification_code'=> in_array($booking->status, [Booking::STATUS_APPROVED, Booking::STATUS_VERIFIED])
                ? $booking->verification_code : null,
            'qr_code_url'      => $qrCodeUrl,
            'rejection_reason' => $booking->rejection_reason,
            'verified_at'      => $booking->verified_at?->toDateTimeString(),
            'can_review'       => $booking->canBeReviewed(),
            'created_at'       => $booking->created_at->toDateTimeString(),
        ];
    }

    private function generateQrCode(Booking $booking): ?string
    {
        try {
            // Gunakan chillerlan/php-qrcode jika tersedia
            if (class_exists('\chillerlan\QRCode\QRCode')) {
                $qr      = new \chillerlan\QRCode\QRCode();
                $content = json_encode([
                    'code'       => $booking->verification_code,
                    'booking_id' => $booking->id,
                    'app'        => 'beautyhub',
                ]);
                $imgData  = $qr->render($content); // base64 PNG
                $imgData  = str_replace('data:image/png;base64,', '', $imgData);
                $path     = 'qrcodes/' . $booking->verification_code . '.png';
                Storage::disk('public')->put($path, base64_decode($imgData));
                $booking->update(['qr_code_path' => $path]);
                return asset('storage/' . $path);
            }
        } catch (\Throwable $e) {
            // Fallback: return null, mobile bisa generate sendiri
        }
        return null;
    }
}
