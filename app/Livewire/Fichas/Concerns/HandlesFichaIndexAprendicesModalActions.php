<?php

namespace App\Livewire\Fichas\Concerns;

use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\Log;

trait HandlesFichaIndexAprendicesModalActions
{
    public function openGestionarAprendices()
    {
        $this->showGestionarAprendicesModal = true;
        $this->loadPersonasDisponibles();
        $this->reset(['selectedPersonas', 'selectAllPersonas', 'searchPersona']);
    }

    public function openGestionarAprendicesDirect($fichaId)
    {
        // Log para depuración - qué ficha se está abriendo
        \Log::info('Abriendo gestión de aprendices - Ficha ID: '.$fichaId);

        // Cargar la ficha con todas las relaciones necesarias incluyendo aprendices
        $this->selectedFicha = FichaCaracterizacion::with(['programaFormacion', 'sede', 'instructor.persona', 'ambiente', 'aprendices.persona'])
            ->withCount('aprendices')
            ->find($fichaId);

        // Log para depuración - qué se cargó
        \Log::info('Ficha cargada:', [
            'ficha_id' => $this->selectedFicha?->id,
            'ficha_codigo' => $this->selectedFicha?->ficha,
            'aprendices_count' => $this->selectedFicha?->aprendices->count(),
            'aprendices_ids' => $this->selectedFicha?->aprendices->pluck('id')->toArray(),
            'aprendices_data' => $this->selectedFicha?->aprendices->map(function ($a) {
                return [
                    'id' => $a->id,
                    'persona_id' => $a->persona_id,
                    'persona_nombre' => $a->persona ? $a->persona->primer_nombre.' '.$a->persona->primer_apellido : 'N/A',
                    'estado' => $a->estado,
                ];
            })->toArray(),
        ]);

        $this->showGestionarAprendicesModal = true;
        $this->loadPersonasDisponibles();
        $this->reset(['selectedPersonas', 'selectAllPersonas', 'searchPersona']);
    }

    public function closeGestionarAprendicesModal()
    {
        $this->showGestionarAprendicesModal = false;
        $this->reset(['selectedPersonas', 'selectAllPersonas', 'searchPersona', 'personasDisponibles', 'selectedAprendicesAsignados', 'selectAllAprendicesAsignados']);
    }
}
