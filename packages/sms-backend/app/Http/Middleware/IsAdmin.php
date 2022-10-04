<?php

namespace App\Http\Middleware;

use App\Exceptions\DefaultTenantRequiredException;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Models\Channel;
use Closure;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     * @throws DefaultTenantRequiredException
     */
    public function handle($request, Closure $next): mixed
    {
        $user = Auth::user();

        if (!$user || !$user->is_admin) abort(403);

        return $next($request);
    }
}