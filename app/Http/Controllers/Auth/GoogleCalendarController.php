<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;

class GoogleCalendarController extends Controller
{
    public function __construct(private GoogleCalendarService $calendar) {}

    public function redirect()
    {
        return redirect($this->calendar->getAuthUrl());
    }

    public function callback(Request $request)
    {
        if (!$request->has('code')) {
            return redirect()->route('profile.edit')
                ->with('error', __('settings.google_calendar_auth_failed'));
        }

        $token = $this->calendar->exchangeCode($request->code);
        auth()->user()->update(['google_calendar_token' => json_encode($token)]);

        return redirect()->route('profile.edit')
            ->with('success', __('settings.google_calendar_connected'));
    }

    public function disconnect()
    {
        auth()->user()->update(['google_calendar_token' => null]);
        return redirect()->route('profile.edit')
            ->with('success', __('settings.google_calendar_disconnected'));
    }
}
