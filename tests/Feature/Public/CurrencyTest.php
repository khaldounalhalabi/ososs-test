<?php

namespace Tests\Feature\Public;

use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Tests\Feature\Contracts\MainTestCase;

class CurrencyTest extends MainTestCase
{
    protected string $model = Currency::class;
    protected string $resource = CurrencyResource::class;
    protected string $userType = 'none';
    protected array $relations = [];
    private string $baseUrl = 'api.currencies.';

    public function test_guest_can_index_currencies()
    {
        $this->multiple()
            ->paginate()
            ->dataResource(Currency::factory(5)->create())
            ->getSuccess()
            ->get(route($this->baseUrl . 'index'))
            ->assertExactJson($this->responseBody)
            ->assertOk();
    }
}
