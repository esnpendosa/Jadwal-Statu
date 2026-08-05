<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// Fallback login handler for native HTML form POST to /admin/login
Route::post('/admin/login', function (Request $request) {
    $email = $request->input('data.email') ?? $request->input('email');
    $password = $request->input('data.password') ?? $request->input('password');
    $remember = (bool) ($request->input('data.remember') ?? $request->input('remember'));

    if ($email && $password && Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
        $request->session()->regenerate();
        return redirect()->intended('/admin');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ]);
});
