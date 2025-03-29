<?php

namespace Tests\Feature\Admin;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\RoleEnum;
use Tests\Feature\Contracts\MainTestCase;

class ProductTest extends MainTestCase
{
    protected string $model = Product::class;
    protected string $resource = ProductResource::class;
    protected string $userType = RoleEnum::ADMIN->value;
    protected array $relations = [];
    private string $baseUrl = 'api.admin.products.';

    public function test_admin_can_index_products()
    {
        $products = Product::factory(5)->create();
        $this->multiple()
            ->paginate()
            ->getSuccess()
            ->relations(['priceLists'])
            ->dataResource($products->load($this->relations))
            ->get(route('api.admin.products.index'))
            ->assertExactJson($this->responseBody)
            ->assertOk();
    }

    public function test_admin_can_show_a_products()
    {
        $this->requestPathHook($this->baseUrl . 'show')
            ->showTest();
    }

    public function test_admin_can_create_a_products()
    {
        $this->requestPathHook($this->baseUrl . 'store')
            ->storeTest();
    }

    public function test_admin_can_update_a_products()
    {
        $this->requestPathHook($this->baseUrl . 'update')
            ->updateTest();
    }

    public function test_admin_can_delete_a_products()
    {
        $this->requestPathHook($this->baseUrl . 'destroy')
            ->deleteTest();
    }
}
