<?php

namespace Database\Factories;

use App\Models\GuiasAprendizaje;
use App\Models\ProgramaFormacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GuiasAprendizaje>
 */
class GuiasAprendizajeFactory extends Factory
{
    protected $model = GuiasAprendizaje::class;

    public function definition(): array
    {
        $userId = User::query()->value('id') ?? User::factory()->create()->id;

        return [
            'codigo' => strtoupper($this->faker->unique()->bothify('GA-####??')),
            'nombre' => $this->faker->unique()->sentence(3),
            'descripcion' => $this->faker->paragraph(),
            'programa_formacion_id' => ProgramaFormacion::factory(),
            'duracion_horas' => $this->faker->numberBetween(40, 200),
            'duracion_meses' => $this->faker->numberBetween(1, 6),
            'objetivo_general' => $this->faker->sentence(),
            'metodologia' => $this->faker->sentence(),
            'evaluacion' => $this->faker->sentence(),
            'status' => true,
            'user_create_id' => $userId,
            'user_edit_id' => $userId,
        ];
    }
}
