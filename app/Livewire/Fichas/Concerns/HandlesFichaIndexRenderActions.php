<?php

namespace App\Livewire\Fichas\Concerns;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use Illuminate\Support\Facades\Auth;

trait HandlesFichaIndexRenderActions
{
    public function render()
    {
        $user = Auth::user();
        $roleNames = $user?->getRoleNames() ?? collect();
        $isOnlyInstructor = $user && $user->hasRole('INSTRUCTOR') && $roleNames->count() === 1;

        $instructorId = null;
        if ($isOnlyInstructor) {
            $instructorId = Instructor::where('persona_id', $user->persona_id)->value('id');
        }

        $query = FichaCaracterizacion::with(['programaFormacion', 'sede', 'instructor.persona', 'ambiente', 'aprendices.persona'])
            ->withCount('aprendices')
            ->when($isOnlyInstructor, function ($query) use ($instructorId) {
                if (! $instructorId) {
                    $query->whereRaw('1 = 0');

                    return;
                }

                // Para INSTRUCTOR (solo rol), mostrar únicamente fichas activas
                $query->where('status', 1);

                $query->where(function ($q) use ($instructorId) {
                    $q->where('instructor_id', $instructorId)
                        ->orWhereHas('instructorFicha', function ($sub) use ($instructorId) {
                            $sub->where('instructor_id', $instructorId);
                        });
                });
            })
            ->when($this->search, function ($query) {
                $query->where('ficha', 'like', '%'.$this->search.'%')
                    ->orWhereHas('programaFormacion', function ($q) {
                        $q->where('nombre', 'like', '%'.$this->search.'%');
                    });
            })
            ->when($this->programaFilter, function ($query) {
                $query->where('programa_formacion_id', $this->programaFilter);
            })
            ->when($this->regionalFilter, function ($query) {
                $query->whereHas('sede.regional', function ($q) {
                    $q->where('id', $this->regionalFilter);
                });
            })
            ->when($this->sedeFilter, function ($query) {
                $query->where('sede_id', $this->sedeFilter);
            })
            ->when(! $isOnlyInstructor && $this->statusFilter !== '', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('id', 'desc');

        $this->fichas = $query->paginate($this->perPage);

        return view('livewire.fichas.ficha-index', [
            'fichas' => $this->fichas,
            'programas' => $this->programas,
            'regionales' => $this->regionales,
            'sedes' => $this->sedes,
        ]);
    }
}
