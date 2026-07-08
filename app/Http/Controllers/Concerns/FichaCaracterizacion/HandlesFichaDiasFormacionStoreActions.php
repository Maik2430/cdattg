<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaDiasFormacionStoreActions
{
    /**
     * Guarda los días de formación de una ficha.
     *
     * @param  string  $id  El ID de la ficha.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function guardarDiasFormacion(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            Log::info('Iniciando guardado de días de formación', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'dias' => $request->dias ?? [],
                'timestamp' => now(),
            ]);

            // Validar datos
            $request->validate([
                'dias' => 'required|array|min:1',
                'dias.*.dia_id' => 'required|exists:parametros,id',
                'dias.*.hora_inicio' => 'required|date_format:H:i',
                'dias.*.hora_fin' => 'required|date_format:H:i|after:dias.*.hora_inicio',
            ], [
                'dias.required' => 'Debe seleccionar al menos un día de formación.',
                'dias.*.dia_id.required' => 'El día es requerido.',
                'dias.*.dia_id.exists' => 'El día seleccionado no existe.',
                'dias.*.hora_inicio.required' => 'La hora de inicio es requerida.',
                'dias.*.hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
                'dias.*.hora_fin.required' => 'La hora de fin es requerida.',
                'dias.*.hora_fin.date_format' => 'La hora de fin debe tener el formato HH:MM.',
                'dias.*.hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);

            // Validar días según jornada
            $configuracionJornadas = $this->obtenerConfiguracionJornadas();
            $jornadaId = $ficha->jornada_id;

            if (isset($configuracionJornadas[$jornadaId])) {
                $diasPermitidos = $configuracionJornadas[$jornadaId]['dias_permitidos'];
                $diasSeleccionados = collect($request->dias)->pluck('dia_id')->toArray();

                $diasNoPermitidos = array_diff($diasSeleccionados, $diasPermitidos);
                if (! empty($diasNoPermitidos)) {
                    $nombresDias = \App\Models\Parametro::whereIn('id', $diasNoPermitidos)->pluck('name')->toArray();

                    return back()->withErrors([
                        'dias' => 'Los días '.implode(', ', $nombresDias).' no están permitidos para la jornada '.$configuracionJornadas[$jornadaId]['nombre'].'.',
                    ]);
                }
            }

            // Eliminar días existentes
            $ficha->diasFormacion()->delete();

            // Crear nuevos días de formación
            foreach ($request->dias as $diaData) {
                $ficha->diasFormacion()->create([
                    'dia_id' => $diaData['dia_id'],
                    'hora_inicio' => $diaData['hora_inicio'],
                    'hora_fin' => $diaData['hora_fin'],
                ]);
            }

            // Calcular y actualizar horas totales
            $nuevasHorasTotales = $this->calcularHorasTotales($ficha->diasFormacion()->get(), $ficha);
            $ficha->update([
                'total_horas' => $nuevasHorasTotales,
                'user_edit_id' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Días de formación guardados exitosamente', [
                'ficha_id' => $id,
                'total_dias' => count($request->dias),
                'horas_totales' => $nuevasHorasTotales,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('fichaCaracterizacion.show', $id)
                ->with('success', 'Días de formación guardados exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Error de validación en guardado de días de formación', [
                'ficha_id' => $id,
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar días de formación', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return back()->withErrors([
                'error' => 'Error al guardar días de formación: '.$e->getMessage(),
            ]);
        }
    }
}
