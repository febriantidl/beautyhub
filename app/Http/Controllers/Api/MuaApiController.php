<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mua;
use Illuminate\Http\Request;

class MuaApiController extends Controller
{
    /**
     * GET /api/muas
     */
    public function index(Request $request)
    {
        $query = Mua::with('user', 'services')
            ->whereHas('user', fn($q) => $q->where('is_active', true));

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('style')) {
            $query->whereJsonContains('style_tags', $request->style);
        }

        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', (float) $request->min_rating);
        }

        if ($request->boolean('verified_only')) {
            $query->where('is_verified', true);
        }

        if ($request->filled('max_price')) {
            $query->whereHas('services', fn($q) =>
                $q->where('price', '<=', $request->max_price)->where('is_active', true)
            );
        }

        $sortField = match($request->get('sort', 'rating')) {
            'rating'     => 'rating',
            'experience' => 'experience_years',
            'reviews'    => 'total_reviews',
            default      => 'rating',
        };
        $query->orderByDesc($sortField);

        $perPage = min((int) $request->get('per_page', 10), 50);
        $muas = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $muas->map(fn($m) => $this->formatMua($m)),
            'meta'    => [
                'current_page' => $muas->currentPage(),
                'last_page'    => $muas->lastPage(),
                'total'        => $muas->total(),
                'per_page'     => $muas->perPage(),
            ],
        ]);
    }

    /**
     * GET /api/muas/{id}
     */
    public function show(int $id)
    {
        $mua = Mua::with('user', 'services', 'portfolios')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $this->formatMua($mua, detail: true),
        ]);
    }

    /**
     * GET /api/muas/{id}/portfolio
     */
    public function portfolio(int $id)
    {
        $mua = Mua::findOrFail($id);
        $portfolios = $mua->portfolios()
            ->orderByDesc('created_at')
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data'    => $portfolios->map(fn($p) => [
                'id'             => $p->id,
                'title'          => $p->title,
                'caption'        => $p->caption,
                'image_url'      => asset('storage/' . $p->image_path),
                'style_category' => $p->style_category,
                'created_at'     => $p->created_at->toDateString(),
            ]),
            'meta' => [
                'current_page' => $portfolios->currentPage(),
                'last_page'    => $portfolios->lastPage(),
                'total'        => $portfolios->total(),
            ],
        ]);
    }


    public function availability(Request $request, $muaId)
{
    $request->validate([
        'date' => 'required|date'
    ]);

    $allSlots = [
        '08:00',
        '10:00',
        '13:00',
        '15:00',
        '18:00'
    ];

    $bookedSlots = \App\Models\Booking::where('mua_id', $muaId)
    ->where('event_date', $request->date)
    ->whereNotIn('status', [
        'rejected',
        'cancelled'
    ])
    ->pluck('time_slot')
    ->toArray();

    $availableSlots = array_values(
        array_diff($allSlots, $bookedSlots)
    );

    return response()->json([
        'success' => true,
        'date' => $request->date,
        'available_slots' => $availableSlots,
        'booked_slots' => $bookedSlots
    ]);
}



    /**
     * GET /api/muas/{id}/reviews
     */
    public function reviews(int $id)
    {
        $mua = Mua::findOrFail($id);
        $reviews = $mua->reviews()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $reviews->map(fn($r) => [
                'id'           => $r->id,
                'rating'       => $r->rating,
                'comment'      => $r->comment,
                'reviewer'     => [
                    'name'   => $r->user->name,
                    'avatar' => $r->user->avatar ? asset('storage/' . $r->user->avatar) : null,
                ],
                'created_at'   => $r->created_at->toDateString(),
            ]),
            'summary' => [
                'average_rating' => $mua->rating,
                'total_reviews'  => $mua->total_reviews,
            ],
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page'    => $reviews->lastPage(),
                'total'        => $reviews->total(),
            ],
        ]);
    }

    private function formatMua(Mua $mua, bool $detail = false): array
    {
        $data = [
            'id'               => $mua->id,
            'name'             => $mua->user->name,
            'avatar'           => $mua->user->avatar ? asset('storage/' . $mua->user->avatar) : null,
            'location'         => $mua->location,
            'bio'              => $mua->bio,
            'experience_years' => $mua->experience_years,
            'rating'           => $mua->rating,
            'total_reviews'    => $mua->total_reviews,
            'style_tags'       => $mua->style_tags ?? [],
            'certificate'      => $mua->certificate,
            'is_verified'      => $mua->is_verified,
            'services'         => $mua->services
                ->where('is_active', true)
                ->map(fn($s) => [
                    'id'          => $s->id,
                    'name'        => $s->name,
                    'description' => $s->description,
                    'price'       => $s->price,
                    'category'    => $s->category,
                ]),
        ];

        if ($detail) {
            $data['portfolios'] = $mua->portfolios
                ->take(12)
                ->map(fn($p) => [
                    'id'             => $p->id,
                    'title'          => $p->title,
                    'image_url'      => asset('storage/' . $p->image_path),
                    'style_category' => $p->style_category,
                ]);
            $data['phone'] = $mua->user->phone;
        }

        return $data;
    }
}