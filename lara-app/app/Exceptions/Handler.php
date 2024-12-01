<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
                Log::error($e);

                return apiResponse()
                    ->failed()
                    ->message(trans('api-messages.item_not_found'))
                    ->notFound()
                    ->getApiResponse();
            }
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            if (request()->wantsJson() || $request->is('api/*')) {
                Log::error($e);

                return apiResponse()
                    ->failed()
                    ->message(trans('api-messages.un_authenticated'))
                    ->unAuthenticated()
                    ->getApiResponse();
            }
        });

        $this->renderable(function (Throwable $e, $request) {
            if (($e instanceof AccessDeniedHttpException || $e instanceof UnauthorizedException || ($e->getStatusCode() == Response::HTTP_UNAUTHORIZED))
                && (request()->wantsJson() || $request->is('api/*'))) {
                Log::error($e);

                return apiResponse()
                    ->failed()
                    ->message(trans('api-messages.un_authorized'))
                    ->unAuthorized()
                    ->getApiResponse();
            }
        });

        $this->renderable(function (ValidationException $e, $request) {
            if (request()->wantsJson() || $request->is('api/*')) {
                Log::error($e);

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
                Log::error($e);

                return apiResponse()
                    ->failed()
                    ->message(trans('api-messages.requested_link_does_not_exist'))
                    ->notFound()
                    ->getApiResponse();
            }
        });

        $this->reportable(function (Throwable $e) {
            Log::error($e);

            return apiResponse()
                ->failed()
                ->serverError()
                ->message(trans('api-messages.internal_server_error'))
                ->getApiResponse();
        });

        $this->reportable(function (\Error $e) {
            Log::error($e);

            return apiResponse()
                ->failed()
                ->serverError()
                ->serverError()
                ->message(trans('api-messages.internal_server_error'))
                ->getApiResponse();

        });

        $this->renderable(function (Throwable $e, $request) {
            if ($e instanceof HttpException && $e->getStatusCode() == Response::HTTP_INTERNAL_SERVER_ERROR) {
                Log::error($e);

                return apiResponse()
                    ->failed()
                    ->serverError()
                    ->message(trans('api-messages.internal_server_error'))
                    ->getApiResponse();
            }
        });
    }
}
