<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductBatchDetail>
 */
class ProductBatchDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_detail_id' => \App\Models\InvoiceDetail::factory(),
            'expiration_date' => $this->faker->dateTimeThisYear(),
            'batch_number' => $this->faker->bothify('BATCH-####'),
            'quantity' => $this->faker->numberBetween(1, 100),
        ];
    }
}
