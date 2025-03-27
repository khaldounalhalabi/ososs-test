<?php

namespace App\Exceptions;

use App\Http\Controllers\ApiController;
use App\Traits\RestTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler
{
    public function __construct()
    {
    }

    public static function create(): static
    {
        return new static();
    }

    public function handleException($request, Throwable $exception): Response|JsonResponse|RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($exception instanceof AuthenticationException || $exception instanceof AuthorizationException) {
            return rest()
                ->notAuthorized()
                ->message(__('site.unauthorized'))
                ->send();
        }

        if ($exception instanceof HttpException) {
            return rest()
                ->badRequest()
                ->message($exception->getMessage())
                ->send();
        }

        if ($exception instanceof HttpResponseException) {
            return rest()
                ->forbidden()
                ->message($exception->getMessage())
                ->send();
        }

        if ($exception instanceof ValidationException) {
            return rest()
                ->data($exception->errors())
                ->validationError()
                ->message($exception->getMessage())
                ->send();
        }

        return rest()
            ->unknownError()
            ->when(app()->environment('local'),
                fn($res) => $res->message($exception->getMessage()),
                fn($res) => $res->message(__("site.unknown_error"))
            )->send();
    }
}
