<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class SetAppLang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

public function handle(Request $request, Closure $next): Response
{
    $supportedLocales = config('app.available_locales', []);
    
    // Get the first language from the Accept-Language header
    $preferredLang = Str::substr($request->header('Accept-Language'), 0, 2);

    if (!in_array($preferredLang, $supportedLocales)) {
        $preferredLang = config('app.fallback_locale', 'en'); // fallback to default
    }

    App::setLocale($preferredLang);

    return $next($request);
}

}
