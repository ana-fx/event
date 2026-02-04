<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        // 1. Check query parameter (Higher priority for SEO/Canonical Links)
        if ($request->has('lang')) {
            $lang = $request->input('lang');
            if (in_array($lang, ['id', 'en'])) {
                $locale = $lang;
                Session::put('locale', $lang);
            }
        }

        // 2. Check session if no query param
        if (!$locale && Session::has('locale')) {
            $locale = Session::get('locale');
        }

        // 3. Check browser preference
        if (!$locale) {
            $locale = $request->getPreferredLanguage(['id', 'en']) ?? 'id';
        }

        // Final validation
        if (!in_array($locale, ['id', 'en'])) {
            $locale = 'id';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
