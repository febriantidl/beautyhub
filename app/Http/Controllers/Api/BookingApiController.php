<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Notifications\BookingStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingApiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mua_id'           => 'required|exists:muas,id',
            'service_id'       => 'required|exists:services,id',
            'booking_date'     => 'required|date_format:Y-m-d',
            'event_date'       => 'required|date_format:Y-m-d',
            'time_slot'        => 'required|string|max:50',
            'location_address' => 'required|string',
            'location_notes'   => 'nullable|string',
            'price'            => 'required|numeric',
            'notes'            => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal!',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $slotExists = Booking::where('mua_id', $request->mua_id)
                ->where('event_date', $request->event_date)
                ->where('time_slot', $request->time_slot)
                ->whereNotIn('status', ['rejected', 'cancelled'])
                ->exists();

            if ($slotExists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Slot sudah dibooking oleh pelanggan lain'
                ], 422);
            }

            $booking = Booking::create([
                'user_id'          => $request->user()->id,
                'mua_id'           => $request->mua_id,
                'service_id'       => $request->service_id,
                'booking_date'     => $request->booking_date,
                'event_date'       => $request->event_date,
                'time_slot'        => $request->time_slot,
                'location_address' => $request->location_address,
                'location_notes'   => $request->location_notes,
                'price'            => $request->price,
                'notes'            => $request->notes,
                'status'           => 'pending',
                'verified'         => false,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Booking berhasil dibuat!',
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
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function myBookings(Request $request)
    {
        $bookings = Booking::with(['mua.user', 'service'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(function ($booking) {
                return [
                    'id'                => $booking->id,
                    'status'            => $booking->status,
                    'booking_date'      => $booking->booking_date,
                    'event_date'        => $booking->event_date,
                    'time_slot'         => $booking->time_slot,
                    'location_address'  => $booking->location_address,
                    'price'             => (int) $booking->price,
                    'booking_code'      => $booking->booking_code,
                    'rejection_reason'  => $booking->rejection_reason,
                    'verification_code' => $booking->verification_code,
                    'mua'               => [
                        'id'   => $booking->mua?->id,
                        'name' => $booking->mua?->name ?? $booking->mua?->user?->name,
                    ],
                    'service'           => [
                        'id'   => $booking->service?->id,
                        'name' => $booking->service?->name,
                    ],
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $bookings,
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $booking = Booking::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Booking dibatalkan.',
        ]);
    }

    public function show($id)
    {
        $booking = Booking::with(['mua.user', 'service'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                => $booking->id,
                'status'            => $booking->status,
                'booking_date'      => $booking->booking_date,
                'event_date'        => $booking->event_date,
                'time_slot'         => $booking->time_slot,
                'price'             => $booking->price,
                'booking_code'      => $booking->booking_code,
                'verification_code' => $booking->verification_code,
                'rejection_reason'  => $booking->rejection_reason,
                'mua'               => [
                    'id'   => $booking->mua?->id,
                    'name' => $booking->mua?->name ?? $booking->mua?->user?->name,
                ],
                'service'           => [
                    'id'   => $booking->service?->id,
                    'name' => $booking->service?->name,
                ],
            ]
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::with(['user', 'mua'])->findOrFail($id);
        $status  = $request->status;

        $booking->update([
            'status'           => $status,
            'rejection_reason' => $request->rejection_reason ?? null,
        ]);

        if ($booking->user) {
            $booking->user->notify(new BookingStatusNotification($booking, $status));
        }

        return response()->json([
            'success' => true,
            'message' => 'Status booking diupdate.',
        ]);
    }
}