<?php

namespace Tests\Feature\Public;

use App\Http\Resources\CountryResource;
use App\Models\Country;
use Tests\Feature\Contracts\MainTestCase;

class CountryTest extends MainTestCase
{
    protected string $model = Country::class;
    protected string $resource = CountryResource::class;
    protected string $userType = 'none';
    protected array $relations = [];
    private string $baseUrl = 'api.countries.';

    public function test_guest_can_index_countries()
    {
        $this->multiple()
            ->paginate()
            ->dataResource(Country::factory(5)->create())
            ->getSuccess()
            ->get(route($this->baseUrl . 'index'))
            ->assertExactJson($this->responseBody)
            ->assertOk();
    }
}
