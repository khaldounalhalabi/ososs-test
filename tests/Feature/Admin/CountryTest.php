<?php

namespace Tests\Feature\Admin;

use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\RoleEnum;
use Tests\Feature\Contracts\MainTestCase;

class CountryTest extends MainTestCase
{
    protected string $model = Country::class;
    protected string $resource = CountryResource::class;
    protected string $userType = RoleEnum::ADMIN->value;
    protected array $relations = [];
    private string $baseUrl = 'api.admin.countries.';

    public function test_admin_can_index_countries()
    {
        $this->multiple()
            ->paginate()
            ->dataResource($this->user->country()->get()->merge(Country::factory(4)->create()))
            ->getSuccess()
            ->get(route($this->baseUrl . 'index'))
            ->assertExactJson($this->responseBody)
            ->assertOk();
    }

    public function test_admin_can_show_a_country()
    {
        $this->requestPathHook($this->baseUrl . 'show')
            ->showTest();
    }

    public function test_admin_can_create_a_country()
    {
        Country::whereNotNull('id')->delete();
        $this->requestPathHook($this->baseUrl . 'store')
            ->storeTest();
    }

    public function test_admin_can_update_a_country()
    {
        $this->requestPathHook($this->baseUrl . 'update')
            ->updateTest();
    }

    public function test_admin_can_delete_a_country()
    {
        $this->requestPathHook($this->baseUrl . 'destroy')
            ->deleteTest();
    }
}
