<?php

namespace App\Http\Controllers\Concerns\AsignacionInstructor;

use App\Models\AsignacionInstructor;
use Illuminate\View\View;

trait HandlesAsignacionInstructorCrudReadActions
{
    public function index(): View
    {
        $asignaciones = AsignacionInstructor::with([
            'ficha.programaFormacion',
            'instructor.persona',
            'competencia',
            'resultadosAprendizaje',
        ])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('asignaciones.index', compact('asignaciones'));
    }

    public function show(AsignacionInstructor $asignacion): View
    {
        $asignacion->load([
            'ficha.programaFormacion',
            'instructor.persona',
            'competencia',
            'resultadosAprendizaje',
        ]);

        return view('asignaciones.show', compact('asignacion'));
    }
}
