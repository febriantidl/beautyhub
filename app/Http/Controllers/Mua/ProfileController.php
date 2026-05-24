<?php

namespace App\Http\Controllers\Mua;

use App\Http\Controllers\Controller;
use App\Models\Mua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mua  = $user->mua ?? Mua::create(['user_id' => $user->id]);
        return view('mua.profile', compact('user', 'mua'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            // ... validasi lainnya ...
        ]);

        $userUpdate = $request->only(['name', 'phone', 'address', 'gender']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $userUpdate['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($userUpdate);

        $mua = $user->mua ?? Mua::create(['user_id' => $user->id]);
        $mua->update($request->only(['location', 'bio', 'experience_years', 'style_tags', 'certificate']));

        return redirect()->route('mua.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('mua.profile')->with('success', 'Password berhasil diubah.');
    }
}