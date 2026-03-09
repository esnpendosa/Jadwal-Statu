<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);
        return back()->with('success', __('profile.updated'));
    }

    public function updateLanguage(Request $request)
    {
        $request->validate(['locale' => 'required|in:id,en,zh']);
        auth()->user()->update(['preferred_language' => $request->locale]);
        return back()->with('success', __('profile.language_updated'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        auth()->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', __('profile.password_updated'));
    }


}
