<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaDiasFormacionDeleteActions
{
    /**
     * Elimina un día de formación específico.
     *
     * @param  string  $id  El ID de la ficha.
     * @param  string  $diaId  El ID del día de formación.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function eliminarDiaFormacion(string $id, string $diaId)
    {
        try {
            DB::beginTransaction();

            Log::info('Iniciando eliminación de día de formación', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'dia_id' => $diaId,
                'timestamp' => now(),
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);
            $diaFormacion = $ficha->diasFormacion()->findOrFail($diaId);

            // Eliminar el día de formación
            $diaFormacion->delete();

            // Recalcular horas totales
            $nuevasHorasTotales = $this->calcularHorasTotales($ficha->diasFormacion()->get(), $ficha);
            $ficha->update([
                'total_horas' => $nuevasHorasTotales,
                'user_edit_id' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Día de formación eliminado exitosamente', [
                'ficha_id' => $id,
                'dia_id' => $diaId,
                'horas_totales' => $nuevasHorasTotales,
                'user_id' => Auth::id(),
            ]);

            return back()->with('success', 'Día de formación eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar día de formación', [
                'ficha_id' => $id,
                'dia_id' => $diaId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return back()->withErrors([
                'error' => 'Error al eliminar día de formación: '.$e->getMessage(),
            ]);
        }
    }
}
