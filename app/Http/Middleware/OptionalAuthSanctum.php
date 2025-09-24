<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class OptionalAuthSanctum
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken()) {
            if(Auth::guard('sanctum')->check()) {
                Auth::shouldUse('sanctum');
            }

            return $next($request);
        }

        return $next($request);
    }
}
