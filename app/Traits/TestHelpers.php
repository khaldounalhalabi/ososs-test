<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @mixin TestCase
 */
trait TestHelpers
{
    use RefreshDatabase;

    /** @var class-string */
    protected string $model;

    protected array $relations = [];

    protected string $requestPath;

    /** @var class-string */
    protected string $resource;

    protected User $user;

    protected string $userType;

    protected bool $isMultiple = false;
    protected bool $detailed = false;

    protected array $pagination = [
        "current_page" => 1,
        "from" => 1,
        "is_first_page" => true,
        "is_last_page" => true,
        "per_page" => 10,
        "to" => 5,
        "total" => 5,
        "total_pages" => 1
    ];

    protected array $responseBody = [
        'data' => null,
        'code' => 200,
        'pagination_data' => null,
    ];

    protected array $headers = [
        'Accept' => 'Application/Json',
        'Content-Type' => 'Application/Json',
        'Accept-Language' => 'en',
    ];

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed RoleSeeder');

        if ($this->userType != 'none') {
            $this->signIn($this->userType);
        }
    }

    /**
     * check if the model can soft-delete
     */
    public function checkSoftDeleteColumn(): bool
    {
        $tableName = (new $this->model())->getTable();
        $columns = Schema::getColumnListing($tableName);

        return in_array('deleted_at', $columns);
    }

    /**
     * this function is for converting the return value of a resource as an array
     * @param mixed $data     the data that  has to be converted
     * @param bool  $multiple if you want to return an array of data
     */
    public function convertResourceToArray(mixed $data, bool $multiple = false): array
    {
        if (!$multiple) {
            $resource = $this->detailed
                ? $this->resource::make($data)->detailed()
                : $this->resource::make($data);
        } else {
            $resource = $this->detailed
                ? $this->resource::collection($data)->detailed()
                : $this->resource::collection($data);
        }

        return json_decode(
            json_encode($resource),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * this function for login using email address and default password is 12345678
     */
    public function login(string $email, string $password = '12345678'): void
    {
        auth('api')->attempt([
            'email' => $email,
            'password' => $password,
        ]);
    }

    /**
     * @param string $routeName the route name
     * @return static
     */
    public function requestPathHook(string $routeName = ''): static
    {
        $this->requestPath = $routeName;
        return $this;
    }

    public function signIn($type = null): void
    {
        $this->user = User::factory()->create();
        if (isset($type) && $type != 'none') {
            $this->user->assignRole($type);
        }
        $this->be($this->user, 'api');
        $this->headers['Authorization'] = "Bearer " . auth('api')->login($this->user);
        $this->user->refresh();
    }

    public function multiple(): static
    {
        $this->isMultiple = true;
        return $this;
    }

    public function detailed(): static
    {
        $this->detailed = true;
        return $this;
    }

    public function relations(array $relations = []): static
    {
        $this->relations = $relations;
        return $this;
    }

    public function single(): static
    {
        $this->isMultiple = false;
        return $this;
    }

    public function paginate($total = 5): static
    {
        $this->pagination['to'] = $total;
        $this->pagination['total'] = $total;
        $this->responseBody['pagination_data'] = $this->pagination;
        return $this;
    }

    public function getSuccess(): static
    {
        $this->responseBody['message'] = trans('site.get_successfully');
        return $this;
    }

    public function statusOk(): static
    {
        $this->responseBody['code'] = Response::HTTP_OK;
        return $this;
    }

    public function updateSuccess(): static
    {
        $this->responseBody['message'] = __('site.update_successfully');
        $this->responseBody['code'] = Response::HTTP_OK;
        return $this;
    }

    public function storeSuccess(): static
    {
        $this->responseBody['message'] = __('site.stored_successfully');
        return $this;
    }

    public function deleteSuccess(): static
    {
        $this->responseBody['message'] = __('site.delete_successfully');
        $this->responseBody['code'] = Response::HTTP_OK;
        return $this;
    }

    public function unPaginate(): static
    {
        $this->responseBody['pagination_data'] = null;
        return $this;
    }

    public function dataResource(Model|Collection|EloquentCollection $data): static
    {
        $this->responseBody['data'] = $this->convertResourceToArray($data, $this->isMultiple);
        return $this;
    }

    /**
     * data = false ||| message  = there is no data
     */
    public function failedFalseResponse(): static
    {
        $this->responseBody['data'] = false;
        $this->responseBody['message'] = __('site.there_is_no_data');
        $this->responseBody['code'] = Response::HTTP_NOT_FOUND;
        return $this;
    }

    /**
     * data = [] ||| message = there is no data
     * @return TestHelpers
     */
    public function failedMultiResponse(): static
    {
        $this->responseBody['data'] = [];
        $this->responseBody['message'] = __('site.there_is_no_data');
        $this->responseBody['code'] = Response::HTTP_OK;
        $this->responseBody['pagination_data'] = null;

        return $this;
    }

    public function successEmptyResponse(): static
    {
        $this->responseBody['data'] = [];
        $this->responseBody['message'] = __('site.get_successfully');
        return $this;
    }

    public function successTrueResponse(): static
    {
        $this->responseBody['data'] = true;
        $this->responseBody['message'] = __('site.success');
        $this->responseBody['code'] = 200;
        return $this;
    }

    public function data(mixed $data): static
    {
        $this->responseBody['data'] = $data;
        return $this;
    }

    public function failedSingleResponse(): static
    {
        $this->responseBody['data'] = null;
        $this->responseBody['message'] = __('site.there_is_no_data');
        $this->responseBody['code'] = 404;
        return $this;
    }

    public function unAuthorizedResponse(): static
    {
        $this->responseBody = [
            'data' => "",
            'code' => 401,
            'message' => "This action is unauthorized.",
            'pagination_data' => null,
            'abilities' => [],
        ];

        return $this;
    }

    public function initResponse(mixed $data = null, string $message = '', int $code = 200, bool $paginate = false): static
    {
        $this->responseBody['data'] = $data;
        $this->responseBody['message'] = $message;
        $this->responseBody['code'] = $code;
        $this->responseBody['pagination_data'] = $paginate ? $this->pagination : null;

        return $this;
    }

    /**
     * @param class-string<JsonResource> $resourceName
     * @return $this
     */
    public function resource(string $resourceName): static
    {
        $this->resource = $resourceName;
        return $this;
    }

    public function successMessage(): static
    {
        $this->responseBody['message'] = __('site.success');
        return $this;
    }

    public function message(string $message): static
    {
        $this->responseBody['message'] = $message;
        return $this;
    }
}
