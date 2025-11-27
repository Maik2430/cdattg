<?php

namespace Database\Factories;

use App\Models\Ambiente;
use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use App\Models\JornadaFormacion;
use App\Models\ProgramaFormacion;
use App\Models\Sede;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FichaCaracterizacion>
 */
class FichaCaracterizacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = FichaCaracterizacion::class;

    public function definition(): array
    {
        try {
            $programaId = ProgramaFormacion::query()->inRandomOrder()->value('id');
        } catch (\Exception $e) {
            $programaId = null;
        }
        
        $modalidades = [18, 19, 20];
        
        try {
            $jornadaId = JornadaFormacion::query()->inRandomOrder()->value('id');
        } catch (\Exception $e) {
            $jornadaId = null;
        }
        
        try {
            $sedeId = Sede::query()->inRandomOrder()->value('id');
        } catch (\Exception $e) {
            $sedeId = null;
        }
        
        try {
            $ambienteId = Ambiente::query()->inRandomOrder()->value('id');
        } catch (\Exception $e) {
            $ambienteId = null;
        }

        $mesesAtras = rand(0, 6);
        $mesesAdelante = rand(0, 2);
        $fechaInicio = date('Y-m-d', strtotime("-{$mesesAtras} months +{$mesesAdelante} months"));
        
        $duracionMeses = rand(12, 24);
        $fechaFin = date('Y-m-d', strtotime($fechaInicio . " +{$duracionMeses} months"));

        return [
            'programa_formacion_id' => $programaId ?? 1,
            'ficha' => '29' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
            'instructor_id' => Instructor::factory(),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'ambiente_id' => $ambienteId ?? 1,
            'modalidad_formacion_id' => $modalidades[array_rand($modalidades)],
            'sede_id' => $sedeId ?? 1,
            'jornada_id' => $jornadaId ?? 1,
            'total_horas' => rand(1200, 3200),
            'user_create_id' => 1,
            'user_edit_id' => 1,
            'status' => (rand(1, 100) <= 90) ? 1 : 0,
        ];
    }
}
