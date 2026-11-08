<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Web requests carry the locale in the session. Stateless API requests
     * read ?locale= first, then the browser's Accept-Language header, so a
     * caller that forgets the parameter still gets a sensible language.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->hasSession()
            ? $request->session()->get('locale')
            : $request->query('locale') ?? $request->getPreferredLanguage(['en', 'id']);

        if (is_string($locale) && in_array($this->language($locale), ['en', 'id'], true)) {
            App::setLocale($this->language($locale));
        }

        return $next($request);
    }

    /**
     * "id-ID" / "en_US" resolve to the bare language the app ships.
     */
    private function language(string $locale): string
    {
        return strtolower(preg_split('/[-_]/', $locale)[0] ?? $locale);
    }
}
