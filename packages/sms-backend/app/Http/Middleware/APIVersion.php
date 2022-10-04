<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class APIVersion
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param string $guard the version used
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $guard)
    {
        session(['is_api' => true]);
        config(['app.api.version' => $guard]);
        return $next($request);
    }
}
