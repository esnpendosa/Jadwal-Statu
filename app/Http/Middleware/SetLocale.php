<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            $locale = $user->preferred_language ?? config('app.locale');
        } else {
            $locale = session('locale', config('app.locale'));
        }

        $supported = ['id', 'en', 'zh'];
        if (!in_array($locale, $supported)) {
            $locale = 'id';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
