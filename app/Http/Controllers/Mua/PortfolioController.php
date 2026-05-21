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
        $portfolios = $mua ? $mua->portfolios()->orderByDesc('created_at')->paginate(12) : collect();

        return view('mua.portfolio.index', compact('portfolios', 'mua'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images'           => 'required|array|min:1|max:10',
            'images.*'         => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'titles'           => 'nullable|array',
            'titles.*'         => 'nullable|string|max:100',
            'captions'         => 'nullable|array',
            'captions.*'       => 'nullable|string|max:300',
            'style_categories' => 'nullable|array',
            'style_categories.*' => 'nullable|in:wedding,graduation,party,photoshoot,formal,natural,glam,other',
        ]);

        $mua     = Auth::user()->mua;
        $created = 0;

        foreach ($request->file('images') as $idx => $image) {
            $path = $image->store('portfolios/' . $mua->id, 'public');

            Portfolio::create([
                'mua_id'         => $mua->id,
                'image_path'     => $path,
                'title'          => $request->titles[$idx] ?? null,
                'caption'        => $request->captions[$idx] ?? null,
                'style_category' => $request->style_categories[$idx] ?? null,
            ]);
            $created++;
        }

        return redirect()->route('mua.portfolio.index')
            ->with('success', "{$created} foto portfolio berhasil diunggah.");
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'title'          => 'nullable|string|max:100',
            'caption'        => 'nullable|string|max:300',
            'style_category' => 'nullable|in:wedding,graduation,party,photoshoot,formal,natural,glam,other',
        ]);

        $mua       = Auth::user()->mua;
        $portfolio = Portfolio::where('mua_id', $mua->id)->findOrFail($id);

        $portfolio->update([
            'title'          => $request->title,
            'caption'        => $request->caption,
            'style_category' => $request->style_category,
        ]);

        return response()->json(['success' => true, 'message' => 'Portfolio diperbarui.']);
    }

    public function destroy(int $id)
    {
        $mua       = Auth::user()->mua;
        $portfolio = Portfolio::where('mua_id', $mua->id)->findOrFail($id);

        Storage::disk('public')->delete($portfolio->image_path);
        $portfolio->delete();

        return redirect()->back()->with('success', 'Foto portfolio dihapus.');
    }
}
