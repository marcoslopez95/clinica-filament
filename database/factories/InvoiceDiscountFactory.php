<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceDiscount>
 */
class InvoiceDiscountFactory extends Factory
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
            'amount' => $this->faker->randomFloat(2, 0, 50),
            'percentage' => $this->faker->randomFloat(2, 0, 20),
            'description' => $this->faker->sentence(),
        ];
    }
}
