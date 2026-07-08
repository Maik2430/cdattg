<?php

namespace App\Livewire\Fichas\Concerns;

trait HandlesFichaIndexInstructoresLoadHelpers
{
    private function loadInstructoresDisponibles()
    {
        if ($this->selectedFichaInstructores) {
            \Log::info('=== CARGANDO INSTRUCTORES DISPONIBLES ===');
            \Log::info('Ficha seleccionada:', [
                'ficha_id' => $this->selectedFichaInstructores->id,
                'ficha_codigo' => $this->selectedFichaInstructores->ficha,
            ]);

            // Obtener instructores que no están asignados a esta ficha
            // Usando la misma lógica que el controlador original
            $instructoresAsignadosIds = $this->selectedFichaInstructores->instructorFicha->pluck('instructor_id')->toArray();

            $this->instructoresDisponibles = \App\Models\Instructor::whereHas('persona', function ($query) {
                $query->where('status', 1);
            })
                ->whereNotIn('instructors.id', $instructoresAsignadosIds) // Especificar la tabla explícitamente
                ->with('persona')
                ->ordenarPorNombre() // Usar el scope del modelo para ordenar
                ->get();

            \Log::info('Instructores disponibles cargados:', [
                'count' => $this->instructoresDisponibles->count(),
                'instructores' => $this->instructoresDisponibles->take(5)->map(function ($instructor) {
                    return [
                        'id' => $instructor->id,
                        'nombre_completo' => $instructor->persona->primer_nombre.' '.$instructor->persona->primer_apellido,
                        'numero_documento' => $instructor->persona->numero_documento,
                        'tipo_documento' => $instructor->persona->tipo_documento,
                    ];
                })->toArray(),
            ]);

            // Verificar también todos los instructores activos para comparación
            $totalInstructoresActivos = \App\Models\Instructor::whereHas('persona', function ($query) {
                $query->where('status', 1);
            })->count();

            \Log::info('Total instructores activos en sistema:', [
                'count' => $totalInstructoresActivos,
            ]);

            // Verificar instructores actuales de esta ficha
            $totalInstructoresFicha = $this->selectedFichaInstructores->instructorFicha->count();

            \Log::info('Instructores actuales de esta ficha:', [
                'ficha_id' => $this->selectedFichaInstructores->id,
                'count' => $totalInstructoresFicha,
                'instructores' => $this->selectedFichaInstructores->instructorFicha->take(3)->map(function ($asignacion) {
                    return [
                        'id' => $asignacion->id,
                        'instructor_id' => $asignacion->instructor_id,
                        'nombre_completo' => $asignacion->instructor->persona->primer_nombre.' '.$asignacion->instructor->persona->primer_apellido,
                        'numero_documento' => $asignacion->instructor->persona->numero_documento,
                    ];
                })->toArray(),
            ]);

            // Verificar la cuenta matemática
            \Log::info('Verificación matemática:', [
                'total_instructores_activos' => $totalInstructoresActivos,
                'instructores_en_ficha' => $totalInstructoresFicha,
                'instructores_disponibles_esperados' => $totalInstructoresActivos - $totalInstructoresFicha,
                'instructores_disponibles_reales' => $this->instructoresDisponibles->count(),
                'diferencia' => ($totalInstructoresActivos - $totalInstructoresFicha) - $this->instructoresDisponibles->count(),
            ]);

            \Log::info('=== FIN CARGA INSTRUCTORES DISPONIBLES ===');
        }
    }
}
