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
            'name'             => 'required|string|max:100',
            'phone'            => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:255',
            'gender'           => 'nullable|in:male,female',
            'avatar'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'location'         => 'nullable|string|max:100',
            'bio'              => 'nullable|string|max:500',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'style_tags'       => 'nullable|array',
            'style_tags.*'     => 'string|max:30',
            'certificate'      => 'nullable|string|max:200',
        ]);

        // Update user fields
        $userUpdate = [
            'name'    => $request->name,
            'phone'   => $request->phone,
            'address' => $request->address,
            'gender'  => $request->gender,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $userUpdate['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($userUpdate);

        // Update MUA profile
        $mua = $user->mua ?? Mua::create(['user_id' => $user->id]);
        $mua->update([
            'location'         => $request->location,
            'bio'              => $request->bio,
            'experience_years' => $request->experience_years ?? 0,
            'style_tags'       => $request->style_tags ?? [],
            'certificate'      => $request->certificate,
        ]);

        return redirect()->route('mua.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('mua.profile')
            ->with('success', 'Password berhasil diubah.');
    }
}
