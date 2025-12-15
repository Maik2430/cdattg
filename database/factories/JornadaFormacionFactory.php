<?php

namespace Database\Factories;

use App\Models\JornadaFormacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JornadaFormacion>
 */
class JornadaFormacionFactory extends Factory
{
    protected $model = JornadaFormacion::class;

    public function definition(): array
    {
        $jornadas = [
            'MAÑANA',
            'TARDE',
            'NOCHE',
            'FINES DE SEMANA',
        ];
        
        return [
            'jornada' => $this->faker->randomElement($jornadas),
        ];
    }
}

