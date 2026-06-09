<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    public function index()
    {
        $mua        = Auth::user()->mua;
        $portfolios = $mua
            ? $mua->portfolios()->orderByDesc('created_at')->paginate(12)
            : collect();
        return view('mua.portfolio.index', compact('portfolios', 'mua'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images'   => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $mua = Auth::user()->mua;

        foreach ($request->file('images') as $image) {
            $path     = $image->store('portfolios/' . $mua->id, 'public');
            $fullPath = storage_path('app/public/' . $path);

            // Hitung histogram warna saat upload
            $histogram = $this->computeColorHistogram($fullPath);

            Portfolio::create([
                'mua_id'          => $mua->id,
                'image_path'      => $path,
                'title'           => $request->title    ?? 'Portfolio',
                'caption'         => $request->caption  ?? null,
                'style_category'  => $request->category ?? null,
                'color_histogram' => json_encode($histogram),
            ]);
        }

        return redirect()->back()->with('success', 'Portfolio berhasil diunggah.');
    }

    public function destroy(int $id)
    {
        $mua       = Auth::user()->mua;
        $portfolio = Portfolio::where('mua_id', $mua->id)->findOrFail($id);
        Storage::disk('public')->delete($portfolio->image_path);
        $portfolio->delete();
        return redirect()->back()->with('success', 'Foto dihapus.');
    }

    // ── API untuk Flutter ──────────────────────────────────────
    public function apiIndex(int $mua_id)
    {
        $portfolios = Portfolio::where('mua_id', $mua_id)->latest()->get();

        return response()->json([
            'success' => true,
            'data'    => $portfolios->map(fn($p) => [
                'id'            => (int) $p->id,
                'image_url'     => asset('storage/' . $p->image_path),
                'title'         => $p->title          ?? '',
                'caption'       => $p->caption        ?? '',
                'style_category'=> $p->style_category ?? 'other',
            ]),
        ]);
    }

    // ── Hitung color histogram (32 bin per channel RGB) ────────
    public function computeColorHistogram(string $imagePath): array
    {
        if (!extension_loaded('gd')) {
            return [];
        }

        try {
            $mime  = mime_content_type($imagePath);
            $image = match (true) {
                str_contains($mime, 'jpeg') => imagecreatefromjpeg($imagePath),
                str_contains($mime, 'png')  => imagecreatefrompng($imagePath),
                str_contains($mime, 'webp') => imagecreatefromwebp($imagePath),
                default                     => null,
            };

            if (!$image) return [];

            // Resize ke 64x64 untuk efisiensi
            $small = imagecreatetruecolor(64, 64);
            imagecopyresampled($small, $image, 0, 0, 0, 0, 64, 64,
                imagesx($image), imagesy($image));
            imagedestroy($image);

            // 32 bin per channel
            $bins = 32;
            $histR = array_fill(0, $bins, 0);
            $histG = array_fill(0, $bins, 0);
            $histB = array_fill(0, $bins, 0);
            $total = 0;

            for ($x = 0; $x < 64; $x++) {
                for ($y = 0; $y < 64; $y++) {
                    $rgb = imagecolorat($small, $x, $y);
                    $r   = ($rgb >> 16) & 0xFF;
                    $g   = ($rgb >> 8)  & 0xFF;
                    $b   = $rgb & 0xFF;

                    $histR[(int) ($r / (256 / $bins))]++;
                    $histG[(int) ($g / (256 / $bins))]++;
                    $histB[(int) ($b / (256 / $bins))]++;
                    $total++;
                }
            }
            imagedestroy($small);

            // Normalisasi
            if ($total > 0) {
                $histR = array_map(fn($v) => $v / $total, $histR);
                $histG = array_map(fn($v) => $v / $total, $histG);
                $histB = array_map(fn($v) => $v / $total, $histB);
            }

            return ['r' => $histR, 'g' => $histG, 'b' => $histB];

        } catch (\Throwable) {
            return [];
        }
    }
}