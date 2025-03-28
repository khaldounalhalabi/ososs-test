<?php

namespace Database\Factories;

use App\Models\PriceList;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'base_price' => fake()->randomNumber(3),
            'description' => fake()->text(),
        ];
    }

    public function withPriceLists(int $count = 1): ProductFactory
    {
        return $this->has(PriceList::factory($count));
    }
}
