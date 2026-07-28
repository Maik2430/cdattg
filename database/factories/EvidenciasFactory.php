<?php

namespace Database\Factories;

use App\Models\Evidencias;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evidencias>
 */
class EvidenciasFactory extends Factory
{
    protected $model = Evidencias::class;

    public function definition(): array
    {
        $userId = User::query()->value('id') ?? User::factory()->create()->id;

        return [
            'nombre' => $this->faker->sentence(3),
            'id_estado' => 1,
            'fecha_evidencia' => now()->toDateString(),
            'user_create_id' => $userId,
            'user_edit_id' => $userId,
        ];
    }
}
