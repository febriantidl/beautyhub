<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mua\PortfolioController;
use App\Models\Mua;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SearchApiController extends Controller
{
    // Ambang batas minimum kemiripan (0–100)
    const MIN_SIMILARITY = 30;

    public function searchByImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Simpan gambar sementara & hitung histogramnya
        $imagePath = $request->file('image')->store('search_temp', 'public');
        $fullPath  = storage_path('app/public/' . $imagePath);

        $portfolioCtrl  = new PortfolioController();
        $queryHistogram = $portfolioCtrl->computeColorHistogram($fullPath);

        Storage::disk('public')->delete($imagePath);

        if (empty($queryHistogram)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses gambar.',
            ], 500);
        }

        // Bandingkan dengan semua portofolio yang punya histogram
        $portfolios = Portfolio::whereNotNull('color_histogram')
            ->with('mua.user', 'mua.services')
            ->get();

        // Hitung similarity tiap portofolio
        $scored = [];
        foreach ($portfolios as $portfolio) {
            $hist = json_decode($portfolio->color_histogram, true);
            if (empty($hist)) continue;

            $similarity = $this->histogramSimilarity($queryHistogram, $hist);
            if ($similarity >= self::MIN_SIMILARITY) {
                $scored[] = [
                    'portfolio'  => $portfolio,
                    'similarity' => $similarity,
                ];
            }
        }

        // Sort dari similarity tertinggi
        usort($scored, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        // Group by MUA — ambil similarity tertinggi per MUA
        $muaMap = [];
        foreach ($scored as $item) {
            $muaId = $item['portfolio']->mua_id;
            if (!isset($muaMap[$muaId]) ||
                $item['similarity'] > $muaMap[$muaId]['similarity']) {
                $muaMap[$muaId] = $item;
            }
        }

        // Sort ulang by similarity
        usort($muaMap, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        // Ambil max 10 MUA
        $muaMap = array_slice($muaMap, 0, 10);

        // Format response
        $results = array_map(function ($item) use ($scored) {
            $mua        = $item['portfolio']->mua;
            $similarity = round($item['similarity']);

            // Ambil 3 portofolio MUA ini yang paling mirip
            $muaPortfolios = array_filter($scored,
                fn($s) => $s['portfolio']->mua_id === $mua->id);
            usort($muaPortfolios,
                fn($a, $b) => $b['similarity'] <=> $a['similarity']);
            $topPortfolios = array_slice($muaPortfolios, 0, 3);

            return [
                'id'               => $mua->id,
                'name'             => $mua->user->name,
                'avatar'           => $mua->user->avatar
                    ? asset('storage/' . $mua->user->avatar)
                    : null,
                'location'         => $mua->location,
                'rating'           => $mua->rating,
                'total_reviews'    => $mua->total_reviews,
                'is_verified'      => $mua->is_verified,
                'similarity'       => $similarity,        // % kemiripan
                'sample_portfolios'=> array_values(array_map(
                    fn($s) => [
                        'image_url'      => asset('storage/' . $s['portfolio']->image_path),
                        'style_category' => $s['portfolio']->style_category,
                        'similarity'     => round($s['similarity']),
                    ],
                    $topPortfolios
                )),
                'services' => $mua->services
                    ->where('is_active', true)
                    ->take(3)
                    ->map(fn($s) => [
                        'name'  => $s->name,
                        'price' => $s->price,
                    ])->values(),
            ];
        }, $muaMap);

        return response()->json([
            'success' => true,
            'total'   => count($results),
            'data'    => array_values($results),
        ]);
    }

    // ── Histogram intersection similarity (0–100) ────────────────
    private function histogramSimilarity(array $h1, array $h2): float
    {
        $intersection = 0.0;

        foreach (['r', 'g', 'b'] as $channel) {
            if (!isset($h1[$channel], $h2[$channel])) continue;
            $bins = min(count($h1[$channel]), count($h2[$channel]));
            for ($i = 0; $i < $bins; $i++) {
                $intersection += min($h1[$channel][$i], $h2[$channel][$i]);
            }
        }

        // Dibagi 3 channel, kali 100 → persen
        return ($intersection / 3) * 100;
    }
}