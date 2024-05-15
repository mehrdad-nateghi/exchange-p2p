<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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
                return apiResponse()
                    ->failed()
                    ->message(trans('api-message.item_not_found'))
                    ->notFound()
                    ->getApiResponse();
            }
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            if (request()->wantsJson() || $request->is('api/*')) {
                return apiResponse()
                    ->failed()
                    ->message(trans('api-message.un_authenticated'))
                    ->unAuthorized()
                    ->getApiResponse();
            }
        });

        $this->renderable(function (ValidationException $e, $request) {
            if (request()->wantsJson() || $request->is('api/*')) {
                return apiResponse()
                    ->failed()
                    ->message($e->getMessage())
                    ->data(['errors' => $e->errors()])
                    ->unProcessableEntity()
                    ->getApiResponse();
            }
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if (request()->wantsJson() || $request->is('api/*')) {
                return apiResponse()
                    ->failed()
                    ->message(trans('api-message.requested_link_does_not_exist'))
                    ->badRequest()
                    ->getApiResponse();
            }
        });

        $this->reportable(function (Throwable $t) {
            if (app()->bound('sentry')) {
                Log::error($t);

                return apiResponse()
                    ->failed()
                    ->serverError()
                    ->message(trans('api-message.internal_server_error'))
                    ->getApiResponse();
            }
        });

        $this->reportable(function (\Error $e) {
            if (app()->bound('sentry')) {
                Log::error($e);

                return apiResponse()
                    ->failed()
                    ->serverError()
                    ->message(trans('api-message.internal_server_error'))
                    ->getApiResponse();
            }
        });
    }
}
