<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sede>
 */
class SedeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sede' => $this->faker->unique()->company(),
            'direccion' => $this->faker->address(),
            'municipio_id' => 1,
            'regional_id' => 1,
            'user_create_id' => 1,
            'user_edit_id' => 1,
            'status' => 1,
        ];
    }
}
