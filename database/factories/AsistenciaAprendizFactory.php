<?php

namespace Database\Factories;

use App\Models\Aprendiz;
use App\Models\AsistenciaAprendiz;
use App\Models\Evidencias;
use App\Models\InstructorFichaCaracterizacion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AsistenciaAprendiz>
 */
class AsistenciaAprendizFactory extends Factory
{
    protected $model = AsistenciaAprendiz::class;

    public function definition(): array
    {
        $horaIngreso = $this->faker->dateTimeBetween('08:00:00', '10:00:00')->format('H:i:s');

        $definition = [
            'instructor_ficha_id' => $this->resolverIdRelacion(
                'instructor_fichas_caracterizacion',
                InstructorFichaCaracterizacion::class
            ),
            'aprendiz_ficha_id' => $this->resolverIdRelacion('aprendices', Aprendiz::class),
            'hora_ingreso' => $horaIngreso,
            'hora_salida' => $this->resolverHoraSalida($horaIngreso),
        ];

        if (Schema::hasColumn('asistencia_aprendices', 'evidencia_id')) {
            $definition['evidencia_id'] = $this->resolverEvidenciaIdOpcional();
        }

        return $definition;
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function resolverIdRelacion(string $table, string $modelClass): int
    {
        if (! Schema::hasTable($table)) {
            return 1;
        }

        try {
            return $modelClass::query()->inRandomOrder()->value('id')
                ?? $modelClass::factory()->create()->id;
        } catch (\Exception) {
            return $modelClass::factory()->create()->id;
        }
    }

    private function resolverEvidenciaIdOpcional(): ?int
    {
        if (! Schema::hasTable('evidencias') || ! $this->faker->boolean(30)) {
            return null;
        }

        try {
            return Evidencias::query()->inRandomOrder()->value('id');
        } catch (\Exception) {
            return null;
        }
    }

    private function resolverHoraSalida(string $horaIngreso): ?string
    {
        if (! $this->faker->boolean(80)) {
            return null;
        }

        return $this->faker->dateTimeBetween($horaIngreso, '18:00:00')->format('H:i:s');
    }
}
