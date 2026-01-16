<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'buy_price' => $this->faker->randomFloat(2, 1, 100),
            'sell_price' => $this->faker->randomFloat(2, 5, 200),
            'unit_id' => \App\Models\Unit::factory(),
            'product_category_id' => \App\Models\ProductCategory::factory(),
            'currency_id' => \App\Models\Currency::factory(),
        ];
    }
}
