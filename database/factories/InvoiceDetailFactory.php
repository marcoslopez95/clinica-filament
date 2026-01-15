<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceDetail>
 */
class InvoiceDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => \App\Models\Invoice::factory(),
            'price' => $this->faker->randomFloat(2, 5, 500),
            'quantity' => $this->faker->randomFloat(2, 1, 10),
            'content_id' => \App\Models\Product::factory(),
            'content_type' => \App\Models\Product::class,
        ];
    }
}
