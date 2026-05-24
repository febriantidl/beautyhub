<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingApiController extends Controller
{
    /**
     * Menyimpan data booking baru yang dikirim dari aplikasi mobile (Flutter Nike)
     */
    public function store(Request $request)
    {
        // 1. Proses Validasi input dari Flutter sesuai dengan kolom wajib database asli beautyhub_db
        $validator = Validator::make($request->all(), [
            'mua_id'           => 'required|exists:muas,id',
            'service_id'       => 'required|exists:services,id',
            'booking_date'     => 'required|date_format:Y-m-d', // Tanggal order dibuat
            'event_date'       => 'required|date_format:Y-m-d', // Tanggal acara make-up
            'time_slot'        => 'required|string|max:50',     // Jam acara (contoh: "10:00")
            'location_address' => 'required|string',            // Alamat lengkap lokasi acara
            'location_notes'   => 'nullable|string',            // Catatan tambahan lokasi (opsional)
            'price'            => 'required|numeric',           // Total harga layanan
            'notes'            => 'nullable|string',            // Catatan tambahan dari customer (opsional)
        ]);

        // Jika data yang dikirim Nike tidak sesuai/kurang kolom, kirim respon error ke Flutter
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal, data tidak sesuai skema database!',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // 2. Insert data pesanan baru ke tabel `bookings`
            $booking = Booking::create([
                'user_id'          => $request->user()->id, // Otomatis deteksi ID Customer dari Bearer Token Login
                'mua_id'           => $request->mua_id,
                'service_id'       => $request->service_id,
                'booking_date'     => $request->booking_date,
                'event_date'       => $request->event_date,
                'time_slot'        => $request->time_slot,
                'location_address' => $request->location_address,
                'location_notes'   => $request->location_notes,
                'price'            => $request->price,
                'notes'            => $request->notes,
                'status'           => 'pending', // Default status awal dari mobile, agar muncul di MUA web panel lu
                'verified'         => false,     // Status verifikasi awal
            ]);

            // 3. Return response sukses berformat JSON yang rapi untuk di-map Nike di Flutter
            return response()->json([
                'status'  => 'success',
                'message' => 'Booking berhasil dibuat! Pesanan Anda telah diteruskan ke pihak MUA di website.',
                'data'    => [
                    'id'               => $booking->id,
                    'user_id'          => $booking->user_id,
                    'mua_id'           => $booking->mua_id,
                    'service_id'       => $booking->service_id,
                    'booking_date'     => $booking->booking_date,
                    'event_date'       => $booking->event_date,
                    'time_slot'        => $booking->time_slot,
                    'location_address' => $booking->location_address,
                    'price'            => (int) $booking->price,
                    'status'           => $booking->status,
                    'created_at'       => $booking->created_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            // Jika ada kegagalan sistem internal database
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server Laravel!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}