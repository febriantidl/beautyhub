<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        $mua      = Auth::user()->mua;
        $services = $mua ? $mua->services()->orderBy('category')->get() : collect();

        return view('mua.services.index', compact('services', 'mua'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'price'       => 'required|integer|min:10000|max:99999999',
            'category'    => 'required|in:wedding,graduation,party,photoshoot,formal,other',
        ]);

        $mua = Auth::user()->mua;

        Service::create([
            'mua_id'      => $mua->id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'category'    => $request->category,
            'is_active'   => true,
        ]);

        return redirect()->route('mua.services.index')
            ->with('success', "Layanan '{$request->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'price'       => 'required|integer|min:10000',
            'category'    => 'required|in:wedding,graduation,party,photoshoot,formal,other',
            'is_active'   => 'boolean',
        ]);

        $mua     = Auth::user()->mua;
        $service = Service::where('mua_id', $mua->id)->findOrFail($id);

        $service->update([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'category'    => $request->category,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return response()->json(['success' => true, 'message' => 'Layanan diperbarui.', 'data' => $service]);
    }

    public function destroy(int $id)
    {
        $mua     = Auth::user()->mua;
        $service = Service::where('mua_id', $mua->id)->findOrFail($id);

        // Soft deactivate jika punya booking aktif
        $hasActiveBookings = $service->bookings()
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasActiveBookings) {
            $service->update(['is_active' => false]);
            return redirect()->back()
                ->with('success', 'Layanan dinonaktifkan (masih ada booking aktif).');
        }

        $service->delete();
        return redirect()->back()->with('success', 'Layanan berhasil dihapus.');
    }
}
