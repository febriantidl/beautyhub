<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $mua = Auth::user()->mua;
        if (!$mua) return redirect()->route('mua.dashboard');

        $query = Booking::with(['customer', 'service'])
            ->where('mua_id', $mua->id);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->whereHas('customer', fn($q) =>
                $q->where('name','like','%'.$request->search.'%')
            );
        }

        $bookings = $query->orderByDesc('created_at')->paginate(15);

        $counts = Booking::where('mua_id', $mua->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(status='pending')   as pending,
                SUM(status='approved')  as approved,
                SUM(status='completed') as completed,
                SUM(status='rejected')  as rejected
            ")->first();

        return view('mua.bookings.index', compact('bookings','counts'));
    }

    public function show(int $id)
    {
        $mua     = Auth::user()->mua;
        $booking = Booking::with(['customer','service','review'])
            ->where('mua_id', $mua->id)->findOrFail($id);

        return view('mua.bookings.show', compact('booking'));
    }

    public function approve(Request $request, int $id)
    {
        $mua     = Auth::user()->mua;
        $booking = Booking::where('mua_id', $mua->id)
            ->where('status', Booking::STATUS_PENDING)->findOrFail($id);

        // Generate verification code
        $code = strtoupper(Str::random(8));
        while (Booking::where('verification_code', $code)->exists()) {
            $code = strtoupper(Str::random(8));
        }

        // Generate QR code jika library tersedia
        $qrPath = null;
        try {
            if (class_exists(\Chillerlan\QRCode\QRCode::class)) {
                $qrDir = storage_path('app/public/qrcodes');
                if (!file_exists($qrDir)) mkdir($qrDir, 0775, true);

                $qrOptions = new \Chillerlan\QRCode\QROptions([
                    'outputType' => \Chillerlan\QRCode\Output\QROutputInterface::OUTPUT_IMAGE_PNG,
                    'scale'      => 8,
                    'imageBase64'=> false,
                ]);

                $qrContent = json_encode([
                    'code'       => $code,
                    'booking_id' => $booking->id,
                    'mua'        => $mua->user->name,
                    'event_date' => $booking->event_date->toDateString(),
                ]);

                $filename = 'qrcodes/qr_' . $booking->id . '_' . time() . '.png';
                (new \Chillerlan\QRCode\QRCode($qrOptions))->render($qrContent, storage_path('app/public/'.$filename));
                $qrPath = $filename;
            }
        } catch (\Throwable $e) {
            // QR opsional — lanjut tanpa QR jika gagal
        }

        $booking->update([
            'status'            => Booking::STATUS_APPROVED,
            'verification_code' => $code,
            'qr_code_path'      => $qrPath,
        ]);

        return redirect()->back()
            ->with('success', "Booking #{$booking->id} disetujui. Kode verifikasi: {$code}");
    }

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

        return redirect()->back()->with('success', "Booking #{$booking->id} ditolak.");
    }

    public function complete(int $id)
    {
        $mua     = Auth::user()->mua;
        $booking = Booking::where('mua_id', $mua->id)
            ->where('status', Booking::STATUS_VERIFIED)->findOrFail($id);

        $booking->update(['status' => Booking::STATUS_COMPLETED]);
        return redirect()->back()->with('success', "Booking #{$booking->id} ditandai selesai.");
    }

    public function verifyQr(Request $request)
    {
        $request->validate(['verification_code' => 'required|string|min:6|max:10']);

        $mua  = Auth::user()->mua;
        $code = strtoupper(trim($request->verification_code));

        $booking = Booking::with('customer','service')
            ->where('mua_id', $mua->id)
            ->where('verification_code', $code)
            ->where('status', Booking::STATUS_APPROVED)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Kode tidak valid atau booking sudah diverifikasi.',
            ], 404);
        }

        $booking->update(['status' => Booking::STATUS_VERIFIED, 'verified_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => '✅ Verifikasi berhasil!',
            'data'    => [
                'booking_id'  => $booking->id,
                'customer'    => $booking->customer->name,
                'service'     => $booking->service?->name ?? 'Custom',
                'event_date'  => $booking->event_date->format('d M Y'),
                'time_slot'   => $booking->time_slot ?? '-',
                'location'    => $booking->location_address,
                'price'       => 'Rp '.number_format($booking->price,0,',','.'),
                'verified_at' => now()->format('d M Y, H:i'),
            ],
        ]);
    }
}
