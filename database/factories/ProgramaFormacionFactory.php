<?php

namespace Database\Factories;

use App\Models\Departamento;
use App\Models\Pais;
use App\Models\Parametro;
use App\Models\ProgramaFormacion;
use App\Models\RedConocimiento;
use App\Models\Regional;
use App\Models\User;
use App\Models\Competencia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProgramaFormacion>
 */
class ProgramaFormacionFactory extends Factory
{
    protected $model = ProgramaFormacion::class;

    public function configure(): static
    {
        return $this->afterCreating(function (ProgramaFormacion $programa) {
            $competenciasIds = Competencia::query()
                ->inRandomOrder()
                ->limit($this->faker->numberBetween(1, 3))
                ->pluck('id')
                ->all();

            if (!empty($competenciasIds)) {
                $programa->competencias()->sync($competenciasIds);
            }
        });
    }

    public function definition(): array
    {
        $horasTotales = $this->faker->numberBetween(800, 2200);
        $horasEtapaLectiva = $this->faker->numberBetween(400, $horasTotales - 200);
        $horasEtapaProductiva = $horasTotales - $horasEtapaLectiva;

        $redConocimientoId = $this->obtenerRedConocimientoId();
        $nivelFormacionId = $this->obtenerNivelFormacionId();

        return [
            'codigo' => (string) $this->faker->unique()->numberBetween(100000, 999999),
            'nombre' => strtoupper($this->faker->unique()->sentence(3)),
            'red_conocimiento_id' => $redConocimientoId,
            'nivel_formacion_id' => $nivelFormacionId,
            'horas_totales' => $horasTotales,
            'horas_etapa_lectiva' => $horasEtapaLectiva,
            'horas_etapa_productiva' => $horasEtapaProductiva,
            'status' => true,
            'user_create_id' => User::query()->value('id') ?? User::factory()->create()->id,
            'user_edit_id' => User::query()->value('id') ?? User::factory()->create()->id,
        ];
    }

    private function obtenerRedConocimientoId(): int
    {
        try {
            $redConocimiento = RedConocimiento::query()->inRandomOrder()->first();
            if ($redConocimiento) {
                return $redConocimiento->id;
            }
        } catch (\Exception $e) {
            // Si hay error al consultar, continuar para crear una nueva
        }

        // Si no existe ninguna, crear una nueva con todas sus dependencias
        $regional = Regional::query()->inRandomOrder()->first();
        if (!$regional) {
            $regional = Regional::factory()->create();
        }

        $usuario = User::query()->inRandomOrder()->first();
        if (!$usuario) {
            $usuario = User::factory()->create();
        }

        return RedConocimiento::query()->create([
            'nombre' => $this->faker->unique()->words(2, true),
            'regionals_id' => $regional->id,
            'user_create_id' => $usuario->id,
            'user_edit_id' => $usuario->id,
            'status' => true,
        ])->id;
    }

    private function obtenerNivelFormacionId(): int
    {
        $nivelTema = \App\Models\ParametroTema::query()
            ->whereHas('parametro', function ($q) {
                $q->whereIn('name', ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO']);
            })
            ->inRandomOrder()
            ->first();

        if ($nivelTema) {
            return $nivelTema->id;
        }

        $parametro = Parametro::query()->firstOrCreate(
            ['name' => 'TÉCNICO'],
            ['status' => 1]
        );

        $tema = \App\Models\Tema::query()->firstOrCreate(
            ['name' => 'NIVEL DE FORMACION'],
            ['status' => true]
        );

        return \App\Models\ParametroTema::query()->firstOrCreate(
            [
                'parametro_id' => $parametro->id,
                'tema_id' => $tema->id,
            ],
            [
                'status' => true,
            ]
        )->id;
    }
}
