<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// Fallback login handler for native HTML form POST to /admin/login
Route::post('/admin/login', function (Request $request) {
    $email = $request->input('data.email') ?? $request->input('email');
    $password = $request->input('data.password') ?? $request->input('password');
    $remember = (bool) ($request->input('data.remember') ?? $request->input('remember'));

    if (!$email || !$password) {
        return back()->withErrors(['email' => 'Email dan password wajib diisi.']);
    }

    $user = User::where('email', $email)->first();

    if ($user) {
        // Direct Hash check or Auth attempt
        if (Hash::check($password, $user->password) || Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();
            return redirect()->intended('/admin');
        }
    }

    return back()->withErrors(['email' => 'Email atau password salah.']);
});
