<?php

namespace Database\Factories;

use App\Models\Ambiente;
use App\Models\ComplementarioOfertado;
use App\Models\JornadaFormacion;
use App\Models\ParametroTema;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ComplementarioOfertado>
 */
class ComplementarioOfertadoFactory extends Factory
{
    protected $model = ComplementarioOfertado::class;

    public function definition(): array
    {
        $nombres = [
            'Auxiliar de Cocina',
            'Acabados en Madera',
            'Confección de Prendas',
            'Mecánica Básica Automotriz',
            'Cultivos de Huertas Urbanas',
            'Normatividad Laboral',
            'Soldadura Básica',
            'Electricidad Residencial',
            'Plomería Básica',
            'Panadería y Repostería',
            'Corte y Confección',
            'Jardinería y Paisajismo',
        ];
        
        $nombre = $this->faker->unique()->randomElement($nombres);
        
        // Obtener IDs reales o crear registros si no existen
        $modalidadId = ParametroTema::where('tema_id', 5)->inRandomOrder()->value('id');
        if (!$modalidadId) {
            // Crear un ParametroTema si no existe
            $parametro = \App\Models\Parametro::first();
            $tema = \App\Models\Tema::firstOrCreate(['name' => 'MODALIDAD']);
            $modalidad = ParametroTema::firstOrCreate([
                'tema_id' => $tema->id,
                'parametro_id' => $parametro ? $parametro->id : \App\Models\Parametro::factory()->create()->id,
            ]);
            $modalidadId = $modalidad->id;
        }
        
        $jornadaId = JornadaFormacion::inRandomOrder()->value('id');
        if (!$jornadaId) {
            $jornada = JornadaFormacion::factory()->create();
            $jornadaId = $jornada->id;
        }
        
        $ambienteId = Ambiente::where('status', 1)->inRandomOrder()->value('id');
        // Si no hay ambientes, usar null (permitir que sea nullable si la migración lo permite)

        return [
            'codigo' => 'COMP' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'nombre' => $nombre,
            'justificacion' => $this->faker->paragraph(2),
            'requisitos_ingreso' => $this->faker->paragraph(2),
            'duracion' => $this->faker->numberBetween(30, 120),
            'cupos' => $this->faker->numberBetween(10, 50),
            'estado' => $this->faker->randomElement([0, 1, 2]),
            'modalidad_id' => $modalidadId,
            'jornada_id' => $jornadaId,
            'ambiente_id' => $ambienteId, // nullable según la migración
        ];
    }

    /**
     * Estado: Sin Oferta
     */
    public function sinOferta(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 0,
        ]);
    }

    /**
     * Estado: Con Oferta
     */
    public function conOferta(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 1,
        ]);
    }

    /**
     * Estado: Cupos Llenos
     */
    public function cuposLlenos(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 2,
        ]);
    }

    /**
     * Con cupos disponibles
     */
    public function conCupos(int $cupos = null): static
    {
        return $this->state(fn (array $attributes) => [
            'cupos' => $cupos ?? $this->faker->numberBetween(20, 50),
            'estado' => 1,
        ]);
    }

    /**
     * Con duración específica
     */
    public function conDuracion(int $horas): static
    {
        return $this->state(fn (array $attributes) => [
            'duracion' => $horas,
        ]);
    }
}


