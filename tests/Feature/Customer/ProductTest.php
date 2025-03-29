<?php

namespace Customer;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\RoleEnum;
use Tests\Feature\Contracts\MainTestCase;

class ProductTest extends MainTestCase
{
    protected string $model = Product::class;
    protected string $resource = ProductResource::class;
    protected string $userType = RoleEnum::CUSTOMER->value;
    protected array $relations = [];
    private string $baseUrl = 'api.customer.products.';

    public function test_admin_can_index_products()
    {
        $products = Product::factory(5)->create();
        $this->multiple()
            ->paginate()
            ->getSuccess()
            ->data($products->map(fn(Product $product) => [
                'applicable_price' => $product->base_price,
                'base_price' => $product->base_price,
                'description' => $product->description,
                'id' => $product->id,
                'name' => $product->name,
            ]))
            ->get(route('api.customer.products.index'))
            ->assertExactJson($this->responseBody)
            ->assertOk();
    }

    public function test_admin_can_show_a_products()
    {
        $this->failedSingleResponse()
            ->get(route('api.customer.products.show', fake()->randomNumber(1)))
            ->assertExactJson($this->responseBody)
            ->assertNotFound();

        $product = Product::factory()->create();
        $this->data([
            'applicable_price' => $product->base_price,
            'base_price' => $product->base_price,
            'description' => $product->description,
            'id' => $product->id,
            'name' => $product->name,
        ])->getSuccess()
            ->statusOk()
            ->get(route('api.customer.products.show', $product->id))
            ->assertExactJson($this->responseBody)
            ->assertOk();
    }
}
