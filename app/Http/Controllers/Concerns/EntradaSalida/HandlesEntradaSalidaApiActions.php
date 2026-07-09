<?php

namespace App\Http\Controllers\Concerns\EntradaSalida;

use App\Models\EntradaSalida;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesEntradaSalidaApiActions
{
    public function apiIndex(Request $request)
    {
        $fichaCaracterizacion = $request->ficha_id;
        $instructor = $request->instructor_id;
        // Obtén todos los registros de entrada/salida del usuario actual
        $registros = EntradaSalida::where('instructor_user_id', $instructor)
            ->where('fecha', Carbon::now()->toDateString())
            ->where('ficha_caracterizacion_id', $fichaCaracterizacion)
            ->where('listado', null)->get();

        return response()->json($registros, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function apiStoreEntradaSalida(Request $request)
    {
        try {
            $datos = [
                'fecha' => Carbon::now()->toDateString(),
                'instructor_user_id' => $request->instructor_user_id,
                'aprendiz' => $request->aprendiz,
                'entrada' => Carbon::now(),
                'ficha_caracterizacion_id' => $request->ficha_caracterizacion_id,
                'ambiente_id' => $request->ambiente_id,
            ];

            $this->entradaSalidaService->registrarEntrada($datos);

            return response()->json(['message' => 'Entrada registrada con éxito'], 200);
        } catch (\Exception $e) {
            Log::error('Error al registrar entrada: '.$e->getMessage());

            return response()->json(['error' => 'Error al registrar entrada'], 500);
        }
    }

    public function apiUpdateEntradaSalida(Request $request)
    {
        try {
            $this->entradaSalidaService->registrarSalida($request->aprendiz);

            return response()->json(['message' => 'Salida registrada con éxito'], 200);
        } catch (\Exception $e) {
            Log::error('Error al registrar salida: '.$e->getMessage());

            return response()->json(['error' => 'Error al registrar salida'], 500);
        }
    }

    public function apiListarEntradaSalida(Request $request)
    {
        // Obtener la fecha actual
        $fechaHoy = Carbon::now()->toDateString();

        // Realizar la consulta inicial
        $entradaSalidas = EntradaSalida::where('fecha', $fechaHoy)
            ->where('instructor_user_id', $request->instructor_user_id)
            ->where('ficha_caracterizacion_id', $request->ficha_caracterizacion_id)
            ->where('ambiente_id', $request->ambiente_id)
            ->where('listado', null)
            ->get();

        try {
            DB::beginTransaction();

            // Verificar si hay resultados en la consulta inicial
            if ($entradaSalidas->isNotEmpty()) {
                foreach ($entradaSalidas as $entradaSalida) {
                    $entradaSalida->update([
                        'listado' => 1,
                    ]);
                }
            }

            DB::commit();

            // Realizar una nueva consulta para verificar las actualizaciones
            $entradaSalidasNew = EntradaSalida::where('fecha', $fechaHoy)
                ->where('instructor_user_id', $request->instructor_user_id)
                ->where('ficha_caracterizacion_id', $request->ficha_caracterizacion_id)
                ->where('ambiente_id', $request->ambiente_id)
                ->where('listado', 1)
                ->get();

            // Verificar si la nueva consulta tiene resultados
            if ($entradaSalidasNew->isNotEmpty()) {
                return response()->json('Listado exitosamente', 200);
            } else {
                return response()->json('No se encontraron registros actualizados', 404);
            }
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al listar entradas/salidas', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Error al listar registros.'], 500);
        }
    }
}
