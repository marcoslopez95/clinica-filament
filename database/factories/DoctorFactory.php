<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'type_document_id' => \App\Models\TypeDocument::factory(),
            'dni' => $this->faker->unique()->nationalId(),
            'born_date' => $this->faker->date(),
            'cost' => $this->faker->randomFloat(2, 20, 200),
            'specialization_id' => \App\Models\Specialization::factory(),
        ];
    }
}
