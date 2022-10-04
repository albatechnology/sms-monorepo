<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    public function render($request, Throwable $e)
    {
        if ($request->wantsJson()) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json(['message' => 'Not Found!'], 404);
            }

            if ($e instanceof AuthorizationException) {
                throw new GenericAuthorizationException($e->getMessage());
            }

            if ($e instanceof HttpException && $e->getStatusCode() == 403) {
                return response()->json(['message' => 'Forbidden!'], 403);
            }
        }

        return parent::render($request, $e);
    }
}
