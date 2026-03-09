<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(string $locale)
    {
        $supported = ['id', 'en'];
        if (!in_array($locale, $supported)) {
            $locale = 'id';
        }

        session(['locale' => $locale]);

        if (auth()->check()) {
            auth()->user()->update(['preferred_language' => $locale]);
        }

        return back();
    }
}
