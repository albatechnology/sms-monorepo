<?php

namespace App\Http\Middleware;

use App\Exceptions\DefaultTenantRequiredException;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Models\Channel;
use Closure;
use Illuminate\Support\Facades\Auth;

class HasDefaultTenant
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

        if (!$user) abort(403);

        if (is_null($user->channel_id)) throw new DefaultTenantRequiredException();

        tenancy()->setActiveTenant(Channel::findOrFail($user->channel_id));
        return $next($request);
    }
}
