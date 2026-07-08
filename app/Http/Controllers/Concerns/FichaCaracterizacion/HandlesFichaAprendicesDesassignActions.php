<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaAprendicesDesassignActions
{
    public function desasignarAprendices(Request $request, $id)
    {
        try {
            Log::info('=== INICIO DESASIGNACIÓN APRENDICES ===', [
                'ficha_id' => $id,
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'timestamp' => now(),
            ]);

            $request->validate([
                'personas' => 'required|array|min:1',
                'personas.*' => 'exists:personas,id',
            ]);

            $personasIds = $request->input('personas');

            Log::info('Validación exitosa, procesando personas', [
                'ficha_id' => $id,
                'personas_ids' => $personasIds,
                'total_personas' => count($personasIds),
                'user_id' => Auth::id(),
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);

            Log::info('Ficha encontrada', [
                'ficha_id' => $ficha->id,
                'numero_ficha' => $ficha->ficha,
                'user_id' => Auth::id(),
            ]);

            return $this->ejecutarDesasignacionAprendices($ficha, $personasIds, $id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('=== ERROR DE VALIDACIÓN EN DESASIGNACIÓN ===', [
                'ficha_id' => $id,
                'request_data' => $request->all(),
                'validation_errors' => $e->errors(),
                'user_id' => Auth::id(),
                'timestamp' => now(),
            ]);

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Error de validación: '.implode(', ', Arr::flatten($e->errors())));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('=== FICHA NO ENCONTRADA ===', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'timestamp' => now(),
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'La ficha de caracterización no existe.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== ERROR CRÍTICO EN DESASIGNACIÓN APRENDICES ===', [
                'ficha_id' => $id,
                'request_data' => $request->all(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'timestamp' => now(),
            ]);

            return redirect()->back()
                ->with('error', 'Error crítico al desasignar aprendices: '.$e->getMessage().'. Revisar logs para más detalles.');
        }
    }
}
