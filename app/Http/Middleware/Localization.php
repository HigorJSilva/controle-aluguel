<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

final class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $availableLocales = ['en', 'pt'];

        $userPreferredLocale = $request->getPreferredLanguage($availableLocales);

        if ($userPreferredLocale) {
            App::setLocale($userPreferredLocale);
        } else {

            App::setLocale(Config::get('app.locale'));
        }

        return $next($request);
    }
}
