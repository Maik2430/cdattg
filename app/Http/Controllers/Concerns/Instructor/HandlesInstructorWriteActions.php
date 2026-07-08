<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Http\Requests\CreateInstructorRequest;
use App\Models\Instructor;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorWriteActions
{
    use HandlesInstructorStoreDataHelpers;

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateInstructorRequest $request): RedirectResponse
    {
        try {
            $datos = $this->buildInstructorStoreData($request);
            $jornadasIds = $this->parseJornadasFromRequest($request);

            $this->instructorService->crear($datos, $jornadasIds);

            return redirect()
                ->route('instructor.index')
                ->with('success', '¡Instructor asignado exitosamente!');
        } catch (Exception $e) {
            Log::error('Error al crear instructor: '.$e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(Instructor $instructor): View|RedirectResponse
    {
        try {
            return view(
                'Instructores.edit',
                ['instructor' => $instructor],
                array_merge(
                    $this->loadEditFormCatalogs($instructor),
                    $this->loadEditJornadaFormCatalogs($instructor)
                )
            );
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de edición de instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('instructor.index')
                ->with('error', 'Error al cargar el formulario de edición. Por favor, inténtelo de nuevo.');
        }
    }
}
