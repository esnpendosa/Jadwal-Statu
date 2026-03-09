<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => __('auth.deactivated')]);
            }

            // Record login history
            LoginHistory::create([
                'user_id'      => $user->id,
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->userAgent(),
                'status'       => 'success',
                'logged_in_at' => now(),
            ]);

            AuditLog::log('login', "User {$user->email} logged in");

            return redirect()->intended(route('dashboard'));
        }

        // Record failed login
        LoginHistory::create([
            'user_id'      => null,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'status'       => 'failed',
            'logged_in_at' => now(),
        ]);

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        AuditLog::log('logout', "User {$user->email} logged out");

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
