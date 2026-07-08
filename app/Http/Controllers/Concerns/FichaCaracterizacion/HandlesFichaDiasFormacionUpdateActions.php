<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaDiasFormacionUpdateActions
{
    /**
     * Actualiza un día de formación específico.
     *
     * @param  string  $id  El ID de la ficha.
     * @param  string  $diaId  El ID del día de formación.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function actualizarDiaFormacion(Request $request, string $id, string $diaId)
    {
        try {
            DB::beginTransaction();

            Log::info('Iniciando actualización de día de formación', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'dia_id' => $diaId,
                'timestamp' => now(),
            ]);

            // Validar datos
            $request->validate([
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            ], [
                'hora_inicio.required' => 'La hora de inicio es requerida.',
                'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
                'hora_fin.required' => 'La hora de fin es requerida.',
                'hora_fin.date_format' => 'La hora de fin debe tener el formato HH:MM.',
                'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);
            $diaFormacion = $ficha->diasFormacion()->findOrFail($diaId);

            // Actualizar el día de formación
            $diaFormacion->update([
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
            ]);

            // Recalcular horas totales
            $nuevasHorasTotales = $this->calcularHorasTotales($ficha->diasFormacion()->get(), $ficha);
            $ficha->update([
                'total_horas' => $nuevasHorasTotales,
                'user_edit_id' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Día de formación actualizado exitosamente', [
                'ficha_id' => $id,
                'dia_id' => $diaId,
                'horas_totales' => $nuevasHorasTotales,
                'user_id' => Auth::id(),
            ]);

            return back()->with('success', 'Día de formación actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar día de formación', [
                'ficha_id' => $id,
                'dia_id' => $diaId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return back()->withErrors([
                'error' => 'Error al actualizar día de formación: '.$e->getMessage(),
            ]);
        }
    }
}
