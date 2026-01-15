<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductServiceDetail>
 */
class ProductServiceDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => \App\Models\Service::factory(),
            'product_id' => \App\Models\Product::factory(),
            'quantity' => $this->faker->randomFloat(2, 0.1, 10),
        ];
    }
}
