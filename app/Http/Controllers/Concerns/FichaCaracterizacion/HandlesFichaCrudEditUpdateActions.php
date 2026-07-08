<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Http\Requests\UpdateFichaCaracterizacionRequest;
use App\Models\FichaCaracterizacion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaCrudEditUpdateActions
{
    public function edit(string $id): View|RedirectResponse
    {
        try {
            Log::info('Acceso al formulario de edición de ficha de caracterización', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'timestamp' => now(),
            ]);

            $ficha = FichaCaracterizacion::with([
                'programaFormacion',
                'instructor.persona',
                'jornadaFormacion.parametro',
                'ambiente.piso.bloque',
                'modalidadFormacion',
                'sede',
            ])->findOrFail($id);

            [
                'programas' => $programas,
                'instructores' => $instructores,
                'ambientes' => $ambientes,
                'sedes' => $sedes,
                'modalidades' => $modalidades,
                'jornadas' => $jornadas,
            ] = $this->loadFichaFormCatalogs();

            Log::info('Datos cargados para edición de ficha', [
                'ficha_id' => $ficha->id,
                'numero_ficha' => $ficha->ficha,
                'instructor_id' => $ficha->instructor_id,
                'instructor_id_type' => gettype($ficha->instructor_id),
                'total_programas' => $programas->count(),
                'total_instructores' => $instructores->count(),
                'total_ambientes' => $ambientes->count(),
                'total_sedes' => $sedes->count(),
                'total_modalidades' => $modalidades->count(),
                'total_jornadas' => $jornadas->count(),
                'user_id' => Auth::id(),
            ]);

            return view('fichas.edit', compact('ficha', 'programas', 'instructores', 'ambientes', 'sedes', 'modalidades', 'jornadas'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Intento de editar ficha de caracterización inexistente', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'La ficha de caracterización solicitada no existe.');
        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de edición de ficha', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->with('error', 'Error al cargar el formulario de edición. Por favor, intente nuevamente.');
        }
    }

    /**
     * Actualiza una ficha de caracterización existente.
     *
     * @param  UpdateFichaCaracterizacionRequest  $request  La solicitud HTTP que contiene los datos de la ficha a actualizar.
     * @param  string  $id  El ID de la ficha de caracterización que se va a actualizar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la lista de fichas de caracterización con un mensaje de éxito.
     */
    public function update(UpdateFichaCaracterizacionRequest $request, string $id): RedirectResponse
    {
        try {
            Log::info('Inicio de actualización de ficha de caracterización', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'request_data' => $request->validated(),
                'timestamp' => now(),
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);

            // Guardar datos originales para el log
            $datosOriginales = [
                'programa_formacion_id' => $ficha->programa_formacion_id,
                'ficha' => $ficha->ficha,
                'fecha_inicio' => $ficha->fecha_inicio,
                'fecha_fin' => $ficha->fecha_fin,
            ];

            DB::beginTransaction();

            $ficha->fill($request->validated());
            $ficha->user_edit_id = Auth::id();

            return $this->finalizeFichaUpdate($ficha, $datosOriginales, $request->validated());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Intento de actualizar ficha de caracterización inexistente', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'La ficha de caracterización solicitada no existe.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar ficha de caracterización', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->validated(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->with('error', 'Ocurrió un error al actualizar la ficha de caracterización. Por favor, intente nuevamente.')
                ->withInput();
        }
    }

    /**
     * @param  array<string, mixed>  $datosOriginales
     * @param  array<string, mixed>  $datosNuevos
     */
    private function finalizeFichaUpdate(FichaCaracterizacion $ficha, array $datosOriginales, array $datosNuevos): RedirectResponse
    {
        if (! $ficha->save()) {
            throw new \RuntimeException('Error al actualizar la ficha en la base de datos.');
        }

        $ficha->syncInstructorLiderToPivot();
        DB::commit();

        Log::info('Ficha de caracterización actualizada exitosamente', [
            'ficha_id' => $ficha->id,
            'datos_originales' => $datosOriginales,
            'datos_nuevos' => $datosNuevos,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('fichaCaracterizacion.index')
            ->with('success', 'Ficha de caracterización actualizada exitosamente.');
    }
}
