<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
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
            'payment_method_id' => \App\Models\PaymentMethod::factory(),
            'currency_id' => \App\Models\Currency::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'exchange' => $this->faker->randomFloat(4, 1, 100),
        ];
    }
}
