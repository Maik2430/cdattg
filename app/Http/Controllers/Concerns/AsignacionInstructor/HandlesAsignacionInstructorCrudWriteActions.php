<?php

namespace App\Http\Controllers\Concerns\AsignacionInstructor;

use App\Http\Requests\StoreAsignacionInstructorRequest;
use App\Http\Requests\UpdateAsignacionInstructorRequest;
use App\Models\AsignacionInstructor;
use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

trait HandlesAsignacionInstructorCrudWriteActions
{
    public function create(): View
    {
        $fichas = FichaCaracterizacion::with('programaFormacion')
            ->orderBy('ficha')
            ->get();

        $instructores = Instructor::with('persona')
            ->orderBy('nombre_completo_cache')
            ->get();

        return view('asignaciones.create', compact('fichas', 'instructores'));
    }

    public function store(StoreAsignacionInstructorRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $existe = AsignacionInstructor::where('ficha_id', $validated['ficha_id'])
            ->where('instructor_id', $validated['instructor_id'])
            ->where('competencia_id', $validated['competencia_id'])
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'La asignación ya existe para esta ficha, instructor y competencia.');
        }

        try {
            DB::transaction(function () use ($validated) {
                $asignacion = AsignacionInstructor::create([
                    'ficha_id' => $validated['ficha_id'],
                    'instructor_id' => $validated['instructor_id'],
                    'competencia_id' => $validated['competencia_id'],
                ]);

                $asignacion->resultadosAprendizaje()->sync($validated['resultados']);
            });
        } catch (\Throwable $e) {
            Log::error('Error al guardar asignación de instructor', [
                'payload' => $validated,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al guardar la asignación. Intente nuevamente.');
        }

        return redirect()
            ->route('asignaciones.instructores.index')
            ->with('success', 'Asignación registrada correctamente.');
    }

    public function edit(AsignacionInstructor $asignacion): View
    {
        $asignacion->load([
            'ficha.programaFormacion',
            'instructor.persona',
            'competencia',
            'resultadosAprendizaje',
        ]);

        $fichas = FichaCaracterizacion::with('programaFormacion')
            ->orderBy('ficha')
            ->get();

        $instructores = Instructor::with('persona')
            ->orderBy('nombre_completo_cache')
            ->get();

        return view('asignaciones.edit', compact('asignacion', 'fichas', 'instructores'));
    }

    public function update(UpdateAsignacionInstructorRequest $request, AsignacionInstructor $asignacion): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $asignacion) {
                $asignacion->update([
                    'ficha_id' => $validated['ficha_id'],
                    'instructor_id' => $validated['instructor_id'],
                    'competencia_id' => $validated['competencia_id'],
                ]);

                $asignacion->resultadosAprendizaje()->sync($validated['resultados']);
            });
        } catch (\Throwable $e) {
            Log::error('Error al actualizar asignación de instructor', [
                'asignacion_id' => $asignacion->id,
                'payload' => $validated,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al actualizar la asignación. Intente nuevamente.');
        }

        return redirect()
            ->route('asignaciones.instructores.show', $asignacion)
            ->with('success', 'Asignación actualizada correctamente.');
    }
}
