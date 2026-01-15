<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceDetailTax>
 */
class InvoiceDetailTaxFactory extends Factory
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
            'name' => 'IVA',
            'percentage' => 16.00,
            'amount' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
