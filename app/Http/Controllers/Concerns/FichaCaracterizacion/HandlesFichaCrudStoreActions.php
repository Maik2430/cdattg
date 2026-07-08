<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Http\Requests\StoreFichaCaracterizacionRequest;
use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaCrudStoreActions
{
    /**
     * Almacena una nueva ficha de caracterización en la base de datos.
     *
     * @param  StoreFichaCaracterizacionRequest  $request  La solicitud HTTP que contiene los datos de la ficha.
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta 'fichaCaracterizacion.index' con un mensaje de éxito.
     */
    public function store(StoreFichaCaracterizacionRequest $request)
    {
        Log::info('=== MÉTODO STORE LLAMADO ===', [
            'user_id' => Auth::id(),
            'all_data' => $request->all(),
            'timestamp' => now(),
        ]);

        try {
            Log::info('Inicio de creación de nueva ficha de caracterización', [
                'user_id' => Auth::id(),
                'request_data' => $request->validated(),
                'timestamp' => now(),
            ]);

            // Validar disponibilidad del ambiente
            $datos = $request->validated();
            if (isset($datos['ambiente_id']) && isset($datos['sede_id']) && isset($datos['jornada_id'])) {
                $ambientesOcupados = FichaCaracterizacion::where('ambiente_id', $datos['ambiente_id'])
                    ->where('sede_id', $datos['sede_id'])
                    ->where('jornada_id', $datos['jornada_id'])
                    ->where('status', 1) // Solo fichas activas
                    ->exists();

                if ($ambientesOcupados) {
                    Log::warning('Intento de crear ficha con ambiente ya ocupado', [
                        'ambiente_id' => $datos['ambiente_id'],
                        'sede_id' => $datos['sede_id'],
                        'jornada_id' => $datos['jornada_id'],
                        'user_id' => Auth::id(),
                    ]);

                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'El ambiente seleccionado ya está siendo utilizado por otra ficha en la misma sede y jornada.');
                }
            }

            DB::beginTransaction();

            $ficha = new FichaCaracterizacion;
            $ficha->fill($request->validated());
            $ficha->user_create_id = Auth::id();
            $ficha->user_edit_id = Auth::id(); // Agregar user_edit_id para creación

            if ($ficha->save()) {
                // Guardar días de formación si se proporcionaron
                if ($request->has('dias_formacion') && is_array($request->dias_formacion)) {
                    Log::info('Guardando días de formación', [
                        'dias_formacion' => $request->dias_formacion,
                        'horarios_completos' => $request->horarios,
                        'all_request' => $request->all(),
                    ]);

                    foreach ($request->dias_formacion as $diaId) {
                        $fichaDia = new \App\Models\FichaDiasFormacion;
                        $fichaDia->ficha_id = $ficha->id;
                        $fichaDia->dia_id = $diaId;

                        // Obtener horarios desde el array
                        $horarios = $request->input('horarios', []);

                        if (isset($horarios[$diaId])) {
                            $fichaDia->hora_inicio = $horarios[$diaId]['hora_inicio'] ?? '08:00:00';
                            $fichaDia->hora_fin = $horarios[$diaId]['hora_fin'] ?? '16:00:00';

                            Log::info("Horario para día {$diaId}", [
                                'hora_inicio' => $fichaDia->hora_inicio,
                                'hora_fin' => $fichaDia->hora_fin,
                            ]);
                        } else {
                            // Horarios por defecto
                            $fichaDia->hora_inicio = '08:00:00';
                            $fichaDia->hora_fin = '16:00:00';

                            Log::warning("No se encontraron horarios para día {$diaId}, usando valores por defecto");
                        }

                        $fichaDia->save();
                    }
                }

                $ficha->syncInstructorLiderToPivot();

                DB::commit();

                Log::info('Ficha de caracterización creada exitosamente', [
                    'ficha_id' => $ficha->id,
                    'numero_ficha' => $ficha->ficha,
                    'programa_id' => $ficha->programa_formacion_id,
                    'dias_formacion' => $request->dias_formacion ?? [],
                    'user_id' => Auth::id(),
                ]);

                return redirect()->route('fichaCaracterizacion.index')
                    ->with('success', 'Ficha de caracterización creada exitosamente.');
            }

            DB::rollBack();
            throw new \Exception('Error al guardar la ficha en la base de datos.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al crear ficha de caracterización', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->validated(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->with('error', 'Ocurrió un error al crear la ficha de caracterización. Por favor, intente nuevamente.')
                ->withInput();
        }
    }
}
