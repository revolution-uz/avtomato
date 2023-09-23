<?php

namespace App\Exceptions;

use App\Http\Helpers\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * @param  $request
     * @param Throwable $e
     * @return JsonResponse|\Illuminate\Http\Response|Response
     * @throws Throwable
     */
    public function render($request, Throwable $e): \Illuminate\Http\Response|JsonResponse|Response
    {
        if ($request->wantsJson() || $request->ajax() || $request->expectsJson() || $request->isJson()) {
//            if ($e instanceof HttpException or $e instanceof UploadFileException) {
//                return ApiResponse::error($e->getMessage());
//            } else
            if ($e instanceof AuthenticationException) {
                return ApiResponse::error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
            }

            if ($e instanceof ValidationException) {
                return ApiResponse::error($e->validator->getMessageBag()->first());
            }

            if ($e instanceof ModelNotFoundException) {
                return ApiResponse::error(__("Not found"), Response::HTTP_NOT_FOUND);
            }
        }

        return parent::render($request, $e);
    }
}
