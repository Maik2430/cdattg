<?php

namespace App\Livewire\Fichas\Concerns;

trait HandlesFichaIndexInstructoresModalActions
{
    public function openGestionarInstructoresDirect($fichaId)
    {
        \Log::info('=== INICIO GESTIÓN INSTRUCTORES ===');
        \Log::info('Abriendo gestión de instructores - Ficha ID: '.$fichaId);

        // Cargar la ficha con todas las relaciones necesarias como lo hace el controlador original
        $this->selectedFichaInstructores = \App\Models\FichaCaracterizacion::with([
            'instructor.persona',
            'instructorFicha.instructor.persona',
            'instructorFicha.instructorFichaDias.dia',
            'diasFormacion.dia',
            'programaFormacion.redConocimiento',
            'sede.regional',
            'jornadaFormacion.parametro',
        ])->find($fichaId);

        if ($this->selectedFichaInstructores) {
            \Log::info('Ficha cargada para gestión de instructores:', [
                'ficha_id' => $this->selectedFichaInstructores->id,
                'ficha_codigo' => $this->selectedFichaInstructores->ficha,
                'programa' => $this->selectedFichaInstructores->programaFormacion->nombre ?? 'N/A',
                'instructores_asignados_count' => $this->selectedFichaInstructores->instructorFicha->count(),
            ]);

            // Obtener instructores ya asignados a esta ficha como lo hace el controlador
            $this->instructoresAsignados = $this->selectedFichaInstructores->instructorFicha()
                ->with(['competencia', 'resultadosAprendizaje'])
                ->with(['instructor.persona', 'instructorFichaDias.dia'])
                ->get();

            // Cargar instructores disponibles
            $this->loadInstructoresDisponibles();

            // Resetear selecciones
            $this->reset(['selectedInstructores', 'selectAllInstructores', 'searchInstructor', 'selectedInstructoresAsignados', 'selectAllInstructoresAsignados']);

            $this->showGestionarInstructoresModal = true;

            \Log::info('Modal de gestión de instructores abierta');
        } else {
            \Log::error('No se pudo cargar la ficha para gestión de instructores');
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No se pudo cargar la información de la ficha',
            ]);
        }

        \Log::info('=== FIN GESTIÓN INSTRUCTORES ===');
    }

    public function closeGestionarInstructoresModal()
    {
        $this->showGestionarInstructoresModal = false;
        $this->reset([
            'selectedFichaInstructores',
            'instructoresDisponibles',
            'selectedInstructores',
            'selectAllInstructores',
            'searchInstructor',
            'instructoresAsignados',
            'selectedInstructoresAsignados',
            'selectAllInstructoresAsignados',
        ]);
    }
}
