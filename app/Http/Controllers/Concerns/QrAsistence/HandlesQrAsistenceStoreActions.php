<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use App\Models\Asistencia;
use App\Models\AsistenciaAprendiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesQrAsistenceStoreActions
{
    public function store(Request $request)
    {
        $data = $request->all();

        if (! $data) {
            return back()->with('Error', 'No hay datos registrados.');
        }

        $asistenciaActiva = Asistencia::deFicha($data['caracterizacion_id'])
            ->activa()
            ->first();

        if (! $asistenciaActiva) {
            return back()->with('error', 'No hay una sesión de asistencia activa para esta ficha.');
        }

        if ($asistenciaActiva->is_finished) {
            return back()->with('error', 'La asistencia ya fue finalizada. No se pueden registrar más ingresos.');
        }

        $asistenciaAprendiz = null;

        foreach ($data['asistencia'] as $asistence) {
            $asistenceData = json_decode($asistence, true);
            Log::info($asistenceData);

            $asistenciaAprendiz = AsistenciaAprendiz::create([
                'asistencia_id' => $asistenciaActiva->id,
                'instructor_ficha_id' => $data['caracterizacion_id'],
                'aprendiz_ficha_id' => $asistenceData['aprendiz_ficha_id'] ?? null,
                'hora_ingreso' => $asistenceData['hora_ingreso'],
                'user_create_id' => auth()->id(),
                'user_edit_id' => auth()->id(),
            ]);

            Log::info('Asistencia de aprendiz registrada:', [
                'asistencia_id' => $asistenciaActiva->id,
                'aprendiz_ficha_id' => $asistenciaAprendiz->aprendiz_ficha_id,
                'hora_ingreso' => $asistenciaAprendiz->hora_ingreso,
            ]);
        }

        if ($asistenciaAprendiz !== null) {
            return back()->with('success', 'Asistencia registrada exitosamente.');
        }

        return back()->with('error', 'Error al registrar la asistencia.');
    }
}
