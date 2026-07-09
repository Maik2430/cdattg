<?php

namespace App\Http\Controllers\Concerns\ProgramaFormacion;

use App\Http\Requests\StoreProgramaFormacionRequest;
use App\Http\Requests\UpdateProgramaFormacionRequest;
use App\Models\ProgramaFormacion;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesProgramaFormacionCrudWriteActions
{
    use HandlesProgramaFormacionBusinessRulesHelpers;
    use HandlesProgramaFormacionFormDataHelpers;

    public function create()
    {
        $redesConocimiento = $this->getProgramaFormacionRedesConocimiento();
        $nivelesFormacion = $this->getProgramaFormacionNivelesFormacion();

        return view('programas.create', compact('redesConocimiento', 'nivelesFormacion'));
    }

    public function store(StoreProgramaFormacionRequest $request): RedirectResponse
    {
        try {
            $datos = [
                'codigo' => $request->input('codigo'),
                'nombre' => $request->input('nombre'),
                'red_conocimiento_id' => $request->input('red_conocimiento_id'),
                'nivel_formacion_id' => $request->input('nivel_formacion_id'),
                'horas_totales' => (int) $request->input('horas_totales'),
                'horas_etapa_lectiva' => (int) $request->input('horas_etapa_lectiva'),
                'horas_etapa_productiva' => (int) $request->input('horas_etapa_productiva'),
                'user_create_id' => Auth::id(),
                'user_edit_id' => Auth::id(),
                'status' => true,
            ];

            $this->programaService->crear($datos);

            return redirect()->route('programa.index')->with('success', 'Programa creado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al crear programa: '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Error al crear programa.');
        }
    }

    public function edit(string $id)
    {
        $programa = ProgramaFormacion::with([
            'competencias' => function ($query) {
                $query->withCount('programasFormacion')
                    ->orderBy('nombre');
            },
        ])->findOrFail($id);

        $redesConocimiento = $this->getProgramaFormacionRedesConocimiento();
        $nivelesFormacion = $this->getProgramaFormacionNivelesFormacion();

        return view('programas.edit', compact('programa', 'redesConocimiento', 'nivelesFormacion'));
    }

    public function update(UpdateProgramaFormacionRequest $request, string $id): RedirectResponse
    {
        try {
            $programaFormacion = ProgramaFormacion::with('competencias')->findOrFail($id);

            $validationErrors = $this->validateProgramaFormacionBusinessRules($request, $programaFormacion);
            if (! empty($validationErrors)) {
                return redirect()->back()->withInput()->withErrors($validationErrors);
            }

            $datosActualizados = [
                'codigo' => $request->input('codigo'),
                'nombre' => $request->input('nombre'),
                'red_conocimiento_id' => $request->input('red_conocimiento_id'),
                'nivel_formacion_id' => $request->input('nivel_formacion_id'),
                'horas_totales' => (int) $request->input('horas_totales'),
                'horas_etapa_lectiva' => (int) $request->input('horas_etapa_lectiva'),
                'horas_etapa_productiva' => (int) $request->input('horas_etapa_productiva'),
                'status' => $request->input('status', $programaFormacion->status),
                'user_edit_id' => Auth::id(),
            ];

            if ($this->programaService->actualizar($programaFormacion, $datosActualizados)) {
                Log::info('Programa de formación actualizado exitosamente', [
                    'programa_id' => $programaFormacion->id,
                    'codigo' => $programaFormacion->codigo,
                    'nombre' => $programaFormacion->nombre,
                    'usuario_id' => Auth::id(),
                ]);

                return redirect()->route('programa.index')->with('success', 'Programa de formación actualizado exitosamente.');
            }

            return redirect()->back()->with('error', 'Error al actualizar el programa de formación.');
        } catch (Exception $e) {
            Log::error('Error al actualizar programa de formación', [
                'programa_id' => $id,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id(),
                'request_data' => $request->all(),
            ]);

            return redirect()->back()->with('error', 'Error interno al actualizar el programa de formación.');
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $programaFormacion = ProgramaFormacion::findOrFail($id);
            $nombrePrograma = $programaFormacion->nombre;

            $validationErrors = $this->validateProgramaFormacionDeletionRules($programaFormacion);
            if (! empty($validationErrors)) {
                return redirect()->back()->withErrors($validationErrors);
            }

            $programaFormacion->competencias()->detach();

            if ($programaFormacion->delete()) {
                Log::info('Programa de formación eliminado exitosamente', [
                    'programa_id' => $id,
                    'nombre' => $nombrePrograma,
                    'usuario_id' => Auth::id(),
                ]);

                return redirect()->route('programa.index')->with('success', 'Programa de formación eliminado exitosamente.');
            }

            return redirect()->back()->with('error', 'Error al eliminar el programa de formación.');
        } catch (Exception $e) {
            Log::error('Error al eliminar programa de formación', [
                'programa_id' => $id,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Error interno al eliminar el programa de formación.');
        }
    }
}
