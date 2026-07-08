<?php

namespace App\Livewire\Fichas\Concerns;

use Illuminate\Support\Facades\Log;

trait HandlesFichaIndexAprendicesLoadHelpers
{
    private function loadPersonasDisponibles()
    {
        if ($this->selectedFicha) {
            \Log::info('=== CARGANDO PERSONAS DISPONIBLES ===');
            \Log::info('Ficha seleccionada:', ['ficha_id' => $this->selectedFicha->id, 'ficha_codigo' => $this->selectedFicha->ficha]);

            // Obtener IDs de personas relacionadas con esta ficha
            $personasRelacionadasIds = [];

            // 1. Aprendices activos en esta ficha
            $aprendicesIds = $this->selectedFicha->aprendices()
                ->where('estado', 1)
                ->pluck('persona_id')
                ->toArray();

            // 2. Instructor líder de la ficha
            $instructorLiderId = $this->selectedFicha->instructor ? $this->selectedFicha->instructor->persona_id : null;

            // 3. Instructores asignados a la ficha
            $instructoresAsignadosIds = $this->selectedFicha->instructorFicha()
                ->with('instructor.persona')
                ->get()
                ->pluck('instructor.persona_id')
                ->toArray();

            // Combinar todos los IDs relacionados
            $personasRelacionadasIds = array_merge($aprendicesIds, [$instructorLiderId], $instructoresAsignadosIds);
            $personasRelacionadasIds = array_filter($personasRelacionadasIds); // Eliminar nulos
            $personasRelacionadasIds = array_unique($personasRelacionadasIds); // Eliminar duplicados

            \Log::info('Personas relacionadas con esta ficha:', [
                'aprendices_ids' => $aprendicesIds,
                'instructor_lider_id' => $instructorLiderId,
                'instructores_asignados_ids' => $instructoresAsignadosIds,
                'todos_ids' => $personasRelacionadasIds,
                'total_relacionados' => count($personasRelacionadasIds),
            ]);

            // Obtener personas que NO son aprendices en NINGUNA ficha
            // Y que NO están relacionadas con esta ficha específica
            $this->personasDisponibles = \App\Models\Persona::where('status', 1)
                ->whereNotIn('id', function ($query) {
                    $query->select('persona_id')
                        ->from('aprendices')
                        ->where('estado', 1); // Solo aprendices activos en cualquier ficha
                })
                ->whereNotIn('id', $personasRelacionadasIds) // Excluir personas relacionadas con esta ficha
                ->orderBy('primer_nombre')
                ->orderBy('primer_apellido')
                ->get();

            \Log::info('Personas disponibles cargadas (personas que NO son aprendices en ninguna ficha Y que NO están relacionadas con esta ficha):', [
                'count' => $this->personasDisponibles->count(),
                'personas' => $this->personasDisponibles->take(5)->map(function ($persona) { // Solo primeras 5 para log
                    return [
                        'id' => $persona->id,
                        'nombre_completo' => $persona->primer_nombre.' '.$persona->primer_apellido,
                        'numero_documento' => $persona->numero_documento,
                        'status' => $persona->status,
                    ];
                })->toArray(),
            ]);

            // Verificar también todas las personas activas para comparación
            $totalPersonasActivas = \App\Models\Persona::where('status', 1)->count();

            \Log::info('Total personas activas en sistema:', [
                'count' => $totalPersonasActivas,
            ]);

            // Verificar aprendices activos en TODAS las fichas
            $totalAprendicesActivos = \App\Models\Aprendiz::where('estado', 1)->count();

            \Log::info('Total aprendices activos en todas las fichas:', [
                'count' => $totalAprendicesActivos,
            ]);

            // Verificar aprendices actuales de esta ficha
            $totalAprendicesFicha = $this->selectedFicha->aprendices->count();

            \Log::info('Aprendices actuales de esta ficha:', [
                'ficha_id' => $this->selectedFicha->id,
                'count' => $totalAprendicesFicha,
                'aprendices' => $this->selectedFicha->aprendices->take(3)->map(function ($aprendiz) { // Solo primeros 3 para log
                    return [
                        'id' => $aprendiz->id,
                        'persona_id' => $aprendiz->persona_id,
                        'nombre_completo' => $aprendiz->persona->primer_nombre.' '.$aprendiz->persona->primer_apellido,
                        'numero_documento' => $aprendiz->persona->numero_documento,
                    ];
                })->toArray(),
            ]);

            // Verificar la cuenta matemática
            \Log::info('Verificación matemática:', [
                'total_personas_activas' => $totalPersonasActivas,
                'total_aprendices_activos' => $totalAprendicesActivos,
                'personas_relacionadas_ficha' => count($personasRelacionadasIds),
                'personas_disponibles_esperadas' => $totalPersonasActivas - $totalAprendicesActivos - count($personasRelacionadasIds),
                'personas_disponibles_reales' => $this->personasDisponibles->count(),
                'diferencia' => ($totalPersonasActivas - $totalAprendicesActivos - count($personasRelacionadasIds)) - $this->personasDisponibles->count(),
            ]);

            \Log::info('=== FIN CARGA PERSONAS DISPONIBLES ===');
        }
    }
}
