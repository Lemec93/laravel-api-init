<?php

namespace App\Exceptions;

use App\Services\RequestResponseService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        'deleted_at',
        'password',
        'password_confirmation',
        'activated_token',
        'is'
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     *
     * @return mixed
     */
    public function render($request, Exception $exception)
    {
        if ($request->expectsJson()) {
            if ($exception instanceof AuthorizationException) {
                return RequestResponseService::unauthorizedRequest(
                    config('message.11-403'),
                    '11-403'
                );
            }

            if ($exception instanceof ModelNotFoundException) {
                $modelClass = explode('\\', $exception->getModel());

                return RequestResponseService::methodNotAllowedRequest(
                    end($modelClass) . __(' not found.'),
                    '12-404'
                );
            }

            if ($exception instanceof NotFoundHttpException) {
                return RequestResponseService::notFoundRequest(
                    config('message.11-404'),
                    '11-404'
                );
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return RequestResponseService::methodNotAllowedRequest(
                    config('message.10-405'),
                    '10-405'
                );
            }

            if ($exception instanceof MaintenanceModeException) {
                return RequestResponseService::serviceUnavailable(
                    config('message.10-503'),
                    '10-503'
                );
            }

            if ($exception instanceof AcceptLanguageException) {
                return RequestResponseService::notAcceptableRequest(
                    config('message.10-406'),
                    '10-406'
                );
            }

            if ($exception instanceof ContentTypeException) {
                return RequestResponseService::notAcceptableRequest(
                    config('message.11-406'),
                    '11-406'
                );
            }

            if ($exception instanceof UserLoginErrorException) {
                return RequestResponseService::userLoginError();
            }

            if ($exception instanceof UserBannedException) {
                return RequestResponseService::bannedUser();
            }

            if ($exception instanceof UserDisabledException) {
                return RequestResponseService::disabledUser();
            }

            if ($exception instanceof ValidationException) {
                return response()->json([
                    'type' => Str::slug(config('message.'. 422), '_'),
                    'message' => __($exception->getMessage()),
                    'errors' => $exception->errors(),
                ], 422);
            }

            if ($exception instanceof \RuntimeException) {
                return response()->json([
                    'type' => Str::slug(config('message.'. 408), '_'),
                    'message' => __($exception->getMessage()),
                ], 408);
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return RequestResponseService::unauthenticated(
                config('message.10-401'),
                '10-401'
            );
        }

        return parent::unauthenticated($request, $exception);
    }
}
