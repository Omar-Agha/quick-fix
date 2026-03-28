<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetFilamentPanelLocale
{
    /**
     * Force the app locale for Filament (admin) UI. Independent of APP_LOCALE / session.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale((string) config('filament.panel_locale', 'en'));

        return $next($request);
    }
}
