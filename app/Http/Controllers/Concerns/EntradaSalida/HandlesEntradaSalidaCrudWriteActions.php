<?php

namespace App\Http\Controllers\Concerns\EntradaSalida;

use App\Http\Requests\StoreEntradaSalidaRequest;
use App\Models\EntradaSalida;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

trait HandlesEntradaSalidaCrudWriteActions
{
    public function storeEntradaSalida($ficha_id, $aprendiz, $ambiente_id, $descripcion)
    {
        try {
            EntradaSalida::create([
                'fecha' => Carbon::now()->toDateString(),
                'instructor_user_id' => Auth::user()->id,
                'aprendiz' => $aprendiz,
                'entrada' => Carbon::now(),
                'ficha_caracterizacion_id' => $ficha_id,
            ]);

            return redirect()->route('entradaSalida.registros', compact('ficha_id', 'ambiente_id', 'descripcion'))->with('success', '¡Registro Exitoso!');
        } catch (QueryException $e) {
            Log::error('Error de base de datos al registrar entrada/salida', ['error' => $e->getMessage()]);

            return redirect()->back()->withErrors(['error' => 'Error de base de datos. Por favor, inténtelo de nuevo.']);
        } catch (Exception $e) {
            Log::error('Error al registrar entrada/salida', ['error' => $e->getMessage()]);

            return redirect()->back()->withErrors(['error' => 'Se produjo un error. Por favor, inténtelo de nuevo.']);
        }
    }

    public function store(StoreEntradaSalidaRequest $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'aprendiz' => 'required|string',
                'ficha_caracterizacion_id' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            EntradaSalida::create([
                'fecha' => Carbon::now()->toDateString(),
                'instructor_user_id' => Auth::user()->id,
                'aprendiz' => $request->input('aprendiz'),
                'entrada' => Carbon::now(),
                'ficha_caracterizacion_id' => $request->input('ficha_caracterizacion_id'),
            ]);

            return redirect()->route('entradaSalida.registros', [
                'ficha_id' => $request->input('ficha_caracterizacion_id'),
            ])->with('success', '¡Registro Exitoso!');
        } catch (QueryException $e) {
            Log::error('Error de base de datos al registrar entrada/salida', ['error' => $e->getMessage()]);

            return redirect()->back()->withErrors(['error' => 'Error de base de datos. Por favor, inténtelo de nuevo.']);
        } catch (Exception $e) {
            Log::error('Error al registrar entrada/salida', ['error' => $e->getMessage()]);

            return redirect()->back()->withErrors(['error' => 'Se produjo un error. Por favor, inténtelo de nuevo.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EntradaSalida $entradaSalida)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntradaSalida $entradaSalida)
    {
        try {
            $this->entradaSalidaService->eliminar($entradaSalida->id);

            return redirect()->back()->with('success', '¡Registro eliminado exitosamente!');
        } catch (Exception $e) {
            Log::error('Error al eliminar entrada/salida: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al eliminar registro.');
        }
    }
}
