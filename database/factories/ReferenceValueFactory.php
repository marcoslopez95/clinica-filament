<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReferenceValue>
 */
class ReferenceValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_id' => \App\Models\Exam::factory(),
            'name' => $this->faker->word(),
            'min_value' => $this->faker->randomFloat(2, 0, 50),
            'max_value' => $this->faker->randomFloat(2, 51, 100),
        ];
    }
}
