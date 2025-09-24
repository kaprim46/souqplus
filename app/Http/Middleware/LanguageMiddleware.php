<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : env('VITE_APP_LANG', 'ar');

        app()->setLocale($local);

        return $next($request);
    }
}
