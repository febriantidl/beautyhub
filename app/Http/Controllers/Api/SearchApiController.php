<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SearchApiController extends Controller
{
    /**
     * POST /api/search/by-image
     */
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

        // Simpan gambar sementara
        $imagePath = $request->file('image')->store('search_temp', 'public');
        $fullPath  = storage_path('app/public/' . $imagePath);

        // Analisis warna dominan
        $dominantColor    = $this->extractDominantColor($fullPath);
        $detectedCategory = $this->mapColorToCategory($dominantColor);
        $detectedMood     = $this->mapColorToMood($dominantColor);

        // Query MUA berdasarkan kategori yang terdeteksi
        $query = Mua::with('user', 'services', 'portfolios')
            ->whereHas('user', fn($q) => $q->where('is_active', true));

        // Filter berdasarkan kategori terdeteksi
        if ($detectedCategory) {
            $query->whereHas('portfolios', fn($q) =>
                $q->where('style_category', $detectedCategory)
            );
        }

        $muas = $query->orderByDesc('rating')->limit(10)->get();

        // Jika hasil < 3, tambah MUA lain tanpa filter
        if ($muas->count() < 3) {
            $existingIds = $muas->pluck('id');
            $extra = Mua::with('user', 'services', 'portfolios')
                ->whereHas('user', fn($q) => $q->where('is_active', true))
                ->whereNotIn('id', $existingIds)
                ->orderByDesc('rating')
                ->limit(5 - $muas->count())
                ->get();
            $muas = $muas->merge($extra);
        }

        // Hapus file temp
        Storage::disk('public')->delete($imagePath);

        return response()->json([
            'success' => true,
            'analysis' => [
                'dominant_color'    => $dominantColor,
                'detected_category' => $detectedCategory,
                'detected_mood'     => $detectedMood,
                'message'           => $this->buildAnalysisMessage($detectedCategory, $detectedMood),
            ],
            'data' => $muas->map(fn($m) => [
                'id'               => $m->id,
                'name'             => $m->user->name,
                'avatar'           => $m->user->avatar
                    ? asset('storage/' . $m->user->avatar)
                    : null,
                'location'         => $m->location,
                'rating'           => $m->rating,
                'total_reviews'    => $m->total_reviews,
                'experience_years' => $m->experience_years,
                'is_verified'      => $m->is_verified,
                'style_tags'       => $m->style_tags ?? [],
                'sample_portfolios'=> $m->portfolios
                    ->filter(fn($p) => !empty($p->image_path))
                    ->take(3)
                    ->map(fn($p) => [
                        'image_url'      => asset('storage/' . $p->image_path),
                        'style_category' => $p->style_category,
                    ])->values(),
                'services' => $m->services
                    ->where('is_active', true)
                    ->take(3)
                    ->map(fn($s) => [
                        'name'  => $s->name,
                        'price' => $s->price,
                    ])->values(),
            ]),
        ]);
    }

    // ─── Ekstrak warna dominan dari gambar ───────────────────────────
    private function extractDominantColor(string $imagePath): array
    {
        if (!extension_loaded('gd')) {
            return ['r' => 128, 'g' => 128, 'b' => 128, 'hex' => '#808080'];
        }

        try {
            $mime = mime_content_type($imagePath);
            $image = match(true) {
                str_contains($mime, 'jpeg') => imagecreatefromjpeg($imagePath),
                str_contains($mime, 'png')  => imagecreatefrompng($imagePath),
                str_contains($mime, 'webp') => imagecreatefromwebp($imagePath),
                default                     => null,
            };

            if (!$image) {
                return ['r' => 128, 'g' => 128, 'b' => 128, 'hex' => '#808080'];
            }

            // Resize ke 50x50 untuk sampling cepat
            $small = imagecreatetruecolor(50, 50);
            imagecopyresampled($small, $image, 0, 0, 0, 0, 50, 50,
                imagesx($image), imagesy($image));
            imagedestroy($image);

            // Sampling pixel di tengah (hindari background)
            $totalR = $totalG = $totalB = $count = 0;
            for ($x = 10; $x < 40; $x += 2) {
                for ($y = 10; $y < 40; $y += 2) {
                    $rgb   = imagecolorat($small, $x, $y);
                    $r     = ($rgb >> 16) & 0xFF;
                    $g     = ($rgb >> 8)  & 0xFF;
                    $b     = $rgb & 0xFF;

                    // Skip warna terlalu terang (background putih) atau gelap
                    $brightness = ($r + $g + $b) / 3;
                    if ($brightness > 240 || $brightness < 20) continue;

                    $totalR += $r;
                    $totalG += $g;
                    $totalB += $b;
                    $count++;
                }
            }
            imagedestroy($small);

            if ($count === 0) {
                return ['r' => 180, 'g' => 140, 'b' => 120, 'hex' => '#B48C78'];
            }

            $r = (int) ($totalR / $count);
            $g = (int) ($totalG / $count);
            $b = (int) ($totalB / $count);

            return [
                'r'   => $r,
                'g'   => $g,
                'b'   => $b,
                'hex' => sprintf('#%02x%02x%02x', $r, $g, $b),
            ];

        } catch (\Throwable) {
            return ['r' => 128, 'g' => 128, 'b' => 128, 'hex' => '#808080'];
        }
    }

    // ─── Map warna ke kategori makeup ────────────────────────────────
    private function mapColorToCategory(array $color): ?string
    {
        $r = $color['r'];
        $g = $color['g'];
        $b = $color['b'];

        // Hitung HSL untuk analisis lebih akurat
        $rNorm = $r / 255;
        $gNorm = $g / 255;
        $bNorm = $b / 255;

        $max = max($rNorm, $gNorm, $bNorm);
        $min = min($rNorm, $gNorm, $bNorm);
        $l   = ($max + $min) / 2;
        $s   = $max === $min ? 0 : ($l < 0.5
            ? ($max - $min) / ($max + $min)
            : ($max - $min) / (2 - $max - $min));

        // Hue
        $h = 0;
        if ($max !== $min) {
            $d = $max - $min;
            if ($max === $rNorm)      $h = (($gNorm - $bNorm) / $d + ($gNorm < $bNorm ? 6 : 0)) / 6;
            elseif ($max === $gNorm)  $h = (($bNorm - $rNorm) / $d + 2) / 6;
            else                      $h = (($rNorm - $gNorm) / $d + 4) / 6;
        }
        $hDeg = $h * 360;

        // Kulit natural/nude → tone hangat, saturasi rendah
        if ($s < 0.25 && $l > 0.4 && $r > $b) {
            return 'Wisuda'; // natural, clean look
        }

        // Merah/pink kuat → Wedding/Formal
        if ($hDeg < 20 || $hDeg > 340) {
            if ($s > 0.4) return 'Wedding';
        }

        // Pink/magenta → Party/Glam
        if ($hDeg >= 300 && $hDeg <= 340 && $s > 0.3) {
            return 'Party';
        }

        // Coklat/bronze → Formal
        if ($hDeg >= 20 && $hDeg <= 45 && $s > 0.2) {
            return 'Formal';
        }

        // Hijau/biru → Photoshoot/Editorial
        if ($hDeg >= 150 && $hDeg <= 260 && $s > 0.25) {
            return 'Photoshoot';
        }

        // Lainnya
        return 'Lainnya';
    }

    // ─── Map warna ke mood makeup ─────────────────────────────────────
    private function mapColorToMood(array $color): string
    {
        $r = $color['r'];
        $g = $color['g'];
        $b = $color['b'];
        $brightness = ($r + $g + $b) / 3;

        if ($r > 180 && $g < 100 && $b < 120) return 'Bold & Dramatic';
        if ($r > 200 && $g > 150 && $b > 130) return 'Natural & Soft';
        if ($r > 180 && $g > 100 && $b > 150) return 'Glamorous & Chic';
        if ($brightness > 180)                 return 'Fresh & Bright';
        if ($brightness < 80)                  return 'Dark & Mysterious';

        return 'Elegant & Classic';
    }

    // ─── Pesan analisis ───────────────────────────────────────────────
    private function buildAnalysisMessage(?string $category, string $mood): string
    {
        $cat = $category ?? 'berbagai gaya';
        return "Wajahmu cocok dengan gaya $mood. Kami menemukan MUA terbaik untuk makeup $cat!";
    }
}