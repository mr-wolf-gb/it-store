<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @var list<string>
     */
    private const SUPPORTED_LOCALES = ['en', 'fr'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $userLocale = $request->user()?->preferred_locale;
        if (is_string($userLocale) && in_array($userLocale, self::SUPPORTED_LOCALES, true)) {
            return $userLocale;
        }

        $sessionLocale = $request->session()->get('locale');
        if (is_string($sessionLocale) && in_array($sessionLocale, self::SUPPORTED_LOCALES, true)) {
            return $sessionLocale;
        }

        $browserLocale = $request->getPreferredLanguage(self::SUPPORTED_LOCALES);
        if (is_string($browserLocale) && in_array($browserLocale, self::SUPPORTED_LOCALES, true)) {
            return $browserLocale;
        }

        return config('app.locale');
    }
}
