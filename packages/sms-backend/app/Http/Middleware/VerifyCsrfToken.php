<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\App;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     * We exclude the endpoint to get a token. Currently this is
     * for the OpenAPI UI page
     *
     * @var array
     */
    protected $except = [
        "/api/*"
    ];

    /**
     * Dont respect CSRF exception on production
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        if (App::environment('production')) return false;

        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
