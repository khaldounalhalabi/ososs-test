<?php

namespace Tests\Feature\Admin;

use App\Http\Resources\PriceListResource;
use App\Http\Resources\ProductResource;
use App\Models\PriceList;
use App\Models\Product;
use App\RoleEnum;
use Tests\Feature\Contracts\MainTestCase;

class PriceListTest extends MainTestCase
{
    protected string $model = PriceList::class;
    protected string $resource = PriceListResource::class;
    protected string $userType = RoleEnum::ADMIN->value;
    protected array $relations = [];
    private string $baseUrl = 'api.admin.price-lists.';

    public function test_admin_can_create_a_price_list()
    {
        $this->requestPathHook($this->baseUrl . 'store')
            ->storeTest();
    }

    public function test_admin_can_delete_a_price_list()
    {
        $this->requestPathHook($this->baseUrl . 'destroy')
            ->deleteTest();
    }
}
