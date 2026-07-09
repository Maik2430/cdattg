<?php

namespace App\Http\Controllers\Concerns\EntradaSalida;

use App\Models\EntradaSalida;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

trait HandlesEntradaSalidaSalidaUpdateActions
{
    public function updateSalida(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'aprendiz' => 'required|string',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $entradaSalida = EntradaSalida::whereExists(function ($query) use ($request) {
                $query->where('aprendiz', $request->input('aprendiz'))
                    ->where('salida', null);
            })->first();
            if ($entradaSalida) {

                $entradaSalida->update([
                    'salida' => Carbon::now(),
                ]);

                return redirect()->route('entradaSalida.registros', ['fichaCaracterizacion' => $entradaSalida->ficha_caracterizacion_id])->with('success', 'Salida Exitosa');
            }

            return redirect()->back()->withErrors(['error' => 'No ha tomado asistencia a este aprendiz.']);
        } catch (QueryException $e) {
            Log::error('Error de base de datos al registrar salida', ['error' => $e->getMessage()]);

            return redirect()->back()->withErrors(['error' => 'Error de base de datos. Por favor, inténtelo de nuevo.']);
        } catch (Exception $e) {
            Log::error('Error al registrar salida', ['error' => $e->getMessage()]);

            return redirect()->back()->withErrors(['error' => 'Se produjo un error. Por favor, inténtelo de nuevo.']);
        }
    }

    public function updateEntradaSalida($aprendiz)
    {
        try {
            $entradaSalida = EntradaSalida::where('aprendiz', $aprendiz)
                ->where('salida', null)->first();

            if ($entradaSalida) {

                $entradaSalida->update([
                    'salida' => Carbon::now(),
                ]);

                return redirect()->route('entradaSalida.registros', ['fichaCaracterizacion' => $entradaSalida->ficha_caracterizacion_id])->with('success', 'Salida Exitosa');
            }

            return redirect()->back()->withErrors(['error' => 'No ha tomado asistencia a este aprendiz.']);
        } catch (QueryException $e) {
            Log::error('Error de base de datos al registrar salida', ['error' => $e->getMessage()]);

            return redirect()->back()->withErrors(['error' => 'Error de base de datos. Por favor, inténtelo de nuevo.']);
        } catch (Exception $e) {
            Log::error('Error al registrar salida', ['error' => $e->getMessage()]);

            return redirect()->back()->withErrors(['error' => 'Se produjo un error. Por favor, inténtelo de nuevo.']);
        }
    }
}
