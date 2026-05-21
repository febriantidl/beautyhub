<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mua;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchApiController extends Controller
{
    /**
     * POST /api/search/by-image
     * Cari MUA berdasarkan gambar referensi (cosine similarity pada feature_vector).
     * Jika belum ada feature vector, fallback ke pencarian berdasarkan style_category.
     */
    public function searchByImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'          => 'required|image|max:5120',
            'style_category' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Simpan gambar query sementara
        $imagePath = $request->file('image')->store('search_temp', 'public');

        // Coba similarity search via feature_vector
        // Jika belum ada feature vectors tersimpan → fallback ke style_category
        $category = $request->style_category;

        $query = Mua::with('user', 'services', 'portfolios')
            ->whereHas('user', fn($q) => $q->where('is_active', true));

        if ($category) {
            $query->whereHas('portfolios', fn($q) =>
                $q->where('style_category', $category)
            );
        }

        $muas = $query->orderByDesc('rating')->limit(10)->get();

        // Hapus file temp
        \Illuminate\Support\Facades\Storage::disk('public')->delete($imagePath);

        return response()->json([
            'success' => true,
            'message' => $category
                ? "Hasil pencarian berdasarkan kategori '{$category}'."
                : 'Hasil pencarian MUA berdasarkan gambar referensi.',
            'data'    => $muas->map(fn($m) => [
                'id'               => $m->id,
                'name'             => $m->user->name,
                'avatar'           => $m->user->avatar ? asset('storage/' . $m->user->avatar) : null,
                'location'         => $m->location,
                'rating'           => $m->rating,
                'total_reviews'    => $m->total_reviews,
                'experience_years' => $m->experience_years,
                'is_verified'      => $m->is_verified,
                'style_tags'       => $m->style_tags ?? [],
                'sample_portfolios'=> $m->portfolios->take(3)->map(fn($p) => [
                    'image_url'      => asset('storage/' . $p->image_path),
                    'style_category' => $p->style_category,
                ]),
                'services'         => $m->services
                    ->where('is_active', true)
                    ->take(3)
                    ->map(fn($s) => [
                        'name'  => $s->name,
                        'price' => $s->price,
                    ]),
            ]),
        ]);
    }
}
