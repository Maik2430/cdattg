<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResultadosAprendizaje>
 */
class ResultadosAprendizajeFactory extends Factory
{
    protected $model = \App\Models\ResultadosAprendizaje::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo' => 'RAP-'.$this->faker->unique()->numerify('####'),
            'nombre' => $this->faker->sentence(3),
            'status' => true,
        ];
    }
}
