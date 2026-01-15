<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoiceable_type' => \App\Models\Patient::class,
            'invoiceable_id' => \App\Models\Patient::factory(),
            'date' => $this->faker->date(),
            'status' => $this->faker->randomElement([1, 2, 3, 4]),
            'total' => $this->faker->randomFloat(2, 10, 1000),
            'full_name' => $this->faker->name(),
            'dni' => $this->faker->numerify('#########'),
            'currency_id' => \App\Models\Currency::factory(),
            'exchange' => $this->faker->randomFloat(4, 1, 100),
            'invoice_type' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'type_document_id' => \App\Models\TypeDocument::factory(),
        ];
    }
}
