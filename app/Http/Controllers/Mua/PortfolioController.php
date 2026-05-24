<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    // --- WEB MANAGEMENT ---
    public function index()
    {
        $mua = Auth::user()->mua;
        $portfolios = $mua ? $mua->portfolios()->orderByDesc('created_at')->paginate(12) : collect();
        return view('mua.portfolio.index', compact('portfolios', 'mua'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $mua = Auth::user()->mua;
        foreach ($request->file('images') as $image) {
            $path = $image->store('portfolios/' . $mua->id, 'public');
            Portfolio::create([
                'mua_id' => $mua->id,
                'image_path' => $path,
                'title' => $request->title ?? 'Portfolio',
            ]);
        }
        return redirect()->back()->with('success', 'Portfolio berhasil diunggah.');
    }

    // --- API UNTUK FLUTTER (BIAR GAK NGACO) ---
    public function apiIndex(int $mua_id)
    {
        $portfolios = Portfolio::where('mua_id', $mua_id)->latest()->get();
        
        return response()->json([
            'success' => true,
            'data' => $portfolios->map(function ($p) {
                return [
                    'id' => (int) $p->id,
                    'imageUrl' => asset('storage/' . $p->image_path), // Pastikan ini yg dibaca Flutter
                    'title' => $p->title ?? '',
                    'caption' => $p->caption ?? '',
                    'styleCategory' => $p->style_category ?? 'other'
                ];
            })
        ]);
    }

    public function destroy(int $id)
    {
        $mua = Auth::user()->mua;
        $portfolio = Portfolio::where('mua_id', $mua->id)->findOrFail($id);
        Storage::disk('public')->delete($portfolio->image_path);
        $portfolio->delete();
        return redirect()->back()->with('success', 'Foto dihapus.');
    }
}