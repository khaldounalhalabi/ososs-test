<?php

namespace App\Modules;


use Closure;
use Illuminate\Http\JsonResponse;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Response;

/**
 * @mixin ApiResponse
 */
class ApiResponse implements JsonSerializable
{
    private int $code;
    private string $message;
    private mixed $data;
    private array|null $paginationData;

    public function __construct()
    {
        $this->code = Response::HTTP_OK;
        $this->message = __('site.success');
        $this->data = null;
        $this->paginationData = null;
    }

    public static function create(): static
    {
        return new static();
    }

    public function ok(): static
    {
        $this->code = Response::HTTP_OK;
        return $this;
    }

    public function unknown(): static
    {
        $this->code = Response::HTTP_INTERNAL_SERVER_ERROR;
        return $this;
    }

    public function notFound(): static
    {
        $this->code = Response::HTTP_NOT_FOUND;
        return $this;
    }

    public function badRequest(): static
    {
        $this->code = Response::HTTP_BAD_REQUEST;
        return $this;
    }

    public function forbidden(): static
    {
        $this->code = Response::HTTP_FORBIDDEN;
        return $this;
    }

    public function notAuthorized(): static
    {
        $this->code = Response::HTTP_UNAUTHORIZED;
        return $this;
    }

    public function validationError(): static
    {
        $this->code = Response::HTTP_UNPROCESSABLE_ENTITY;
        return $this;
    }

    public function tokenExpiration(): static
    {
        $this->code = Response::HTTP_NOT_ACCEPTABLE;
        return $this;
    }

    public function unverifiedEmail(): static
    {
        $this->code = 407;
        return $this;
    }

    public function message(string|null $message = null): static
    {
        $this->message = $message ?? __('site.success');
        return $this;
    }

    public function data(mixed $data = null): static
    {
        $this->data = $data;
        return $this;
    }

    public function paginationData(array $paginationData): static
    {
        $this->paginationData = $paginationData;
        return $this;
    }

    public function noData(mixed $data = null): static
    {
        $this->data = $data;
        $this->message = __('site.there_is_no_data');
        $this->code = Response::HTTP_NOT_FOUND;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'data' => $this->data,
            'message' => $this->message,
            'code' => $this->code,
            'pagination_data' => $this->paginationData,
        ];
    }

    public function getSuccess(): static
    {
        $this->message = __('site.get_successfully');
        return $this;
    }

    public function storeSuccess(): static
    {
        $this->message = __('site.stored_successfully');
        return $this;
    }

    public function updateSuccess(): static
    {
        $this->message = __('site.update_successfully');
        return $this;
    }

    public function deleteSuccess(): static
    {
        $this->message = __('site.delete_successfully');
        return $this;
    }

    public function send(): JsonResponse
    {
        return response()
            ->json($this, $this->code);
    }

    public function paginatedSuccessfully(mixed $data, array $paginationData): JsonResponse
    {
        return $this->ok()
            ->getSuccess()
            ->data($data['data'])
            ->paginationData($data['pagination_data'])
            ->send();
    }


    public function createdSuccessfully(mixed $data = null): JsonResponse
    {
        return $this->ok()
            ->data($data)
            ->storeSuccess()
            ->send();
    }

    public function getSuccessfully(mixed $data = null): JsonResponse
    {
        return $this->ok()
            ->data($data)
            ->getSuccess()
            ->send();
    }

    public function updatedSuccessfully(mixed $data = null): JsonResponse
    {
        return $this->ok()
            ->data($data)
            ->updateSuccess()
            ->send();
    }

    public function deleteSuccessfully(mixed $data = true): JsonResponse
    {
        return $this->ok()
            ->data($data)
            ->deleteSuccess()
            ->send();
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return self::create()->{$name}(...$arguments);
    }

    public function unknownError(): static
    {
        $this->message(__('site.failed'));
        return $this;
    }

    /**
     * @param bool|Closure(ApiResponse):(ApiResponse) $condition
     * @param Closure(ApiResponse):(ApiResponse)      $then
     * @param Closure(ApiResponse):(ApiResponse)|null $else
     * @return $this
     */
    public function when(bool|Closure $condition, Closure $then, ?Closure $else = null): static
    {
        if (is_callable($condition)) {
            $condition = $condition($this);
        }

        if ($condition) {
            return $then($this);
        }

        if ($else) {
            return $else($this);
        }

        return $this;
    }
}
