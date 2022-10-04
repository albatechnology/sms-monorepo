<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;

class Impersonate
{
    /**
     * If user is an admin, we allow them to impersonate as another user
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user){
            return $next($request);
        }

        if (!$user->is_admin){
            return $next($request);
        }

        if (empty($request->get('impersonate'))){
            return $next($request);
        }

        try{
            Auth::setUser(User::find($request->get('impersonate')));
        }catch (Exception){

        }

        return $next($request);
    }
}