<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if (request()->wantsJson() || $request->is('api/*')) {
                return responseService()
                    ->setStatusToFailed()
                    ->setMessage('Item Not Found')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->getApiResponse();
            }
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            if (request()->wantsJson() || $request->is('api/*')) {
                return responseService()
                    ->setStatusToFailed()
                    ->setMessage('unAuthenticated')
                    ->setStatusCode(Response::HTTP_UNAUTHORIZED)
                    ->getApiResponse();
            }
        });

        $this->renderable(function (ValidationException $e, $request) {
            if (request()->wantsJson() || $request->is('api/*')) {
                return responseService()
                    ->setStatusToFailed()
                    ->setMessage($e->getMessage())
                    ->setData(['errors' => $e->errors()])
                    ->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->getApiResponse();
            }
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if (request()->wantsJson() || $request->is('api/*')) {
                return responseService()
                    ->setStatusToFailed()
                    ->setMessage('The requested link does not exist')
                    ->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->getApiResponse();
            }
        });
    }
}
