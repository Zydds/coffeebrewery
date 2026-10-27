<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    public function __invoke(string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, ['en', 'id'], true), 404);

        session()->put('locale', $locale);
        App::setLocale($locale);

        return back(fallback: route('home'));
    }
}
