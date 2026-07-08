<?php

namespace App\Livewire\Instructores\Concerns;

use App\Models\Instructor;
use App\Models\RedConocimiento;

trait HandlesInstructorIndexModalOpenEspecialidadFichaActions
{
    public function openEspecialidadesModal($instructorId)
    {
        $this->selectedInstructor = Instructor::with([
            'persona',
            'regional',
        ])->find($instructorId);

        if (! $this->selectedInstructor) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Instructor no encontrado',
            ]);

            return;
        }

        $especialidades = $this->selectedInstructor->especialidades ?? [];
        $this->especialidadesAsignadas = [
            'principal' => $especialidades['principal'] ?? null,
            'secundarias' => $especialidades['secundarias'] ?? [],
        ];

        $this->redesConocimientoDisponibles = RedConocimiento::where('status', true)
            ->orderBy('nombre')
            ->get();

        $this->showEspecialidadesModal = true;
    }

    public function openFichasModal($instructorId)
    {
        $this->selectedInstructor = Instructor::with([
            'persona',
            'regional',
            'instructorFichas.ficha.programaFormacion',
        ])->find($instructorId);

        if (! $this->selectedInstructor) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Instructor no encontrado',
            ]);

            return;
        }

        $this->fichasAsignadas = $this->selectedInstructor->instructorFichas()
            ->with('ficha.programaFormacion')
            ->get();

        $this->showFichasModal = true;
    }
}
