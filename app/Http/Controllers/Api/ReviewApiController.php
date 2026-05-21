<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReviewApiController extends Controller
{
    /**
     * POST /api/reviews
     * Customer memberikan ulasan untuk booking yang sudah selesai.
     */
    public function store(Request $request)
    {
        $user = JWTAuth::user();

        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer|exists:bookings,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Pastikan booking milik user ini dan statusnya completed
        $booking = Booking::where('user_id', $user->id)
            ->where('status', Booking::STATUS_COMPLETED)
            ->findOrFail($request->booking_id);

        // Cegah double review
        if ($booking->review) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memberikan ulasan untuk booking ini.',
            ], 422);
        }

        $review = Review::create([
            'booking_id' => $booking->id,
            'user_id'    => $user->id,
            'mua_id'     => $booking->mua_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        // Update rating agregat MUA
        $booking->mua->recalculateRating();

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil dikirim. Terima kasih!',
            'data'    => [
                'id'         => $review->id,
                'rating'     => $review->rating,
                'comment'    => $review->comment,
                'created_at' => $review->created_at->toDateTimeString(),
            ],
        ], 201);
    }
}
