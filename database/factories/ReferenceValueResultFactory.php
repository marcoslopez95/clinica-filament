<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReferenceValueResult>
 */
class ReferenceValueResultFactory extends Factory
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
            'reference_value_id' => \App\Models\ReferenceValue::factory(),
            'result' => (string) $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
