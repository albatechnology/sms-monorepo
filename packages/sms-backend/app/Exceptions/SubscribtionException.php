<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscribtionException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        // ...
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): Response
    {
        // return response(/* ... */);
        if ($request->wantsJson()) {
            return response()->json(['message' => 'You have exceeded the subscription limit!'], 400);
        }
        return response()->view('errors.subscribtion', [], 400);
    }
}
