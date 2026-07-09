<?php

namespace App\Http\Controllers\Concerns\EntradaSalida;

use App\Models\FichaCaracterizacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesEntradaSalidaExportActions
{
    public function crearCarpetaUser()
    {
        $user_id = Auth::id(); // Obtener el ID del usuario autenticado

        $carpeta_csv = public_path('csv');
        $carpeta_usuario = public_path('csv/'.$user_id);

        if (! file_exists($carpeta_csv)) {
            mkdir($carpeta_csv, 0777, true);
        }

        if (! file_exists($carpeta_usuario)) {
            mkdir($carpeta_usuario, 0777, true);
            // echo "Carpeta del usuario creada correctamente.";
        } else {
            // echo "La carpeta del usuario ya existe.";
        }
    }

    public function generarCSV($ficha)
    {
        try {
            $datos = [
                'instructor_user_id' => Auth::id(),
                'ficha_caracterizacion_id' => $ficha,
                'fecha' => Carbon::now()->toDateString(),
            ];

            $response = $this->exportService->exportarEntradaSalidasCSV($datos);

            $this->entradaSalidaService->marcarComoListadas($datos);

            return $response;
        } catch (\Exception $e) {
            Log::error('Error al generar CSV: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al generar CSV.');
        }
    }

    public function marcarListado($ficha)
    {
        DB::table('entrada_salidas')
            ->where('instructor_user_id', Auth::user()->id)
            ->where('fecha', Carbon::now()->toDateString())
            ->where('ficha_caracterizacion_id', $ficha)
            ->update(['listado' => 1]);
    }

    public function destroyFichaCaractrizacion()
    {
        FichaCaracterizacion::where('user_id', Auth::user()->id)->delete();
    }
}
