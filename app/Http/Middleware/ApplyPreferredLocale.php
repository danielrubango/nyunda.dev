<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyPreferredLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.supported_locales', ['fr', 'en']);

        $resolvedLocale = null;

        $sessionLocale = $request->session()->get('preferred_locale');

        if (is_string($sessionLocale) && in_array($sessionLocale, $supportedLocales, true)) {
            $resolvedLocale = $sessionLocale;
        }

        if (! is_string($resolvedLocale)) {
            $userLocale = $request->user()?->preferred_locale;

            if (is_string($userLocale) && in_array($userLocale, $supportedLocales, true)) {
                $resolvedLocale = $userLocale;
            }
        }

        if (! is_string($resolvedLocale)) {
            $resolvedLocale = in_array('fr', $supportedLocales, true)
                ? 'fr'
                : ((string) config('app.locale', 'fr'));
        }

        if (! in_array($resolvedLocale, $supportedLocales, true)) {
            $resolvedLocale = (string) ($supportedLocales[0] ?? 'fr');
        }

        app()->setLocale($resolvedLocale);

        return $next($request);
    }
}
