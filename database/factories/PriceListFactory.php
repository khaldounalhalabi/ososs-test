<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Currency;
use App\Models\PriceList;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceListFactory extends Factory
{
    protected $model = PriceList::class;

    public function definition(): array
    {
        $date = fake()->boolean;
        return [
            'price' => fake()->randomNumber(3),
            'start_date' => $date ? now()->subDays(rand(0, 15)) : null,
            'end_date' => $date ? now()->addDays(rand(0, 15)) : null,
            'priority' => 0,
            'product_id' => Product::factory(),
            'country_code' => fake()->boolean ? Country::inRandomOrder()->first()->code : null,
            'currency_code' => fake()->boolean ? Currency::inRandomOrder()->first()->code : null,
        ];
    }
}
