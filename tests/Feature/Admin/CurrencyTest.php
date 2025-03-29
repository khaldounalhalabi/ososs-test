<?php

namespace Admin;

use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use App\RoleEnum;
use Tests\Feature\Contracts\MainTestCase;

class CurrencyTest extends MainTestCase
{
    protected string $model = Currency::class;
    protected string $resource = CurrencyResource::class;
    protected string $userType = RoleEnum::ADMIN->value;
    protected array $relations = [];
    private string $baseUrl = 'api.admin.currencies.';

    public function test_admin_can_index_currencies()
    {
        $this->multiple()
            ->paginate()
            ->dataResource($this->user->currency()->get()->merge(Currency::factory(4)->create()))
            ->getSuccess()
            ->get(route($this->baseUrl . 'index'))
            ->assertExactJson($this->responseBody)
            ->assertOk();
    }

    public function test_admin_can_show_a_currency()
    {
        $this->requestPathHook($this->baseUrl . 'show')
            ->showTest();
    }

    public function test_admin_can_create_a_currency()
    {
        Currency::whereNotNull('id')->delete();
        $this->requestPathHook($this->baseUrl . 'store')
            ->storeTest();
    }

    public function test_admin_can_update_a_currency()
    {
        $this->requestPathHook($this->baseUrl . 'update')
            ->updateTest();
    }

    public function test_admin_can_delete_a_currency()
    {
        $this->requestPathHook($this->baseUrl . 'destroy')
            ->deleteTest();
    }
}
