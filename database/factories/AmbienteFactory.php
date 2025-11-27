<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ambiente>
 */
class AmbienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'AMBIENTE ' . $this->faker->unique()->numberBetween(100, 999),
            'piso_id' => 1,
            'user_create_id' => 1,
            'user_edit_id' => 1,
            'status' => 1,
        ];
    }
}
