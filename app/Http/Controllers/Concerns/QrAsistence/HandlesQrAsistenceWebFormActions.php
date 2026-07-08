<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use App\Models\AsistenciaAprendiz;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait HandlesQrAsistenceWebFormActions
{
    public function redirectAprenticeExit(string $identificacion, string $ingreso, string $fecha)
    {
        $fecha = Carbon::parse($fecha)->format('Y-m-d');
        $asistencia = AsistenciaAprendiz::where('numero_identificacion', $identificacion)
            ->where('hora_ingreso', $ingreso)
            ->whereDate('created_at', $fecha)
            ->first();

        if (! $asistencia) {
            return back()->with('error', 'No se encontró asistencia con los datos proporcionados.');
        }

        return view('qr_asistence.newExitAsistence', compact('asistencia'));
    }

    public function redirectAprenticeEntrance(string $identificacion, string $ingreso, string $fecha)
    {
        $fecha = Carbon::parse($fecha)->format('Y-m-d');
        $asistencia = AsistenciaAprendiz::where('numero_identificacion', $identificacion)
            ->where('hora_ingreso', $ingreso)
            ->whereDate('created_at', $fecha)
            ->first();

        if (! $asistencia) {
            return back()->with('error', 'No se encontró asistencia con los datos proporcionados.');
        }

        return view('qr_asistence.newEntranceAsistence', compact('asistencia'));
    }

    public function exitFormationAsistenceWeb(string $caracterizacion_id)
    {
        $fechaActual = Carbon::now()->format('Y-m-d');

        $asistencias = AsistenciaAprendiz::where('caracterizacion_id', $caracterizacion_id)
            ->whereDate('created_at', $fechaActual)
            ->get();

        if ($asistencias->isEmpty()) {
            return back()->with('error', 'No se encontraron asistencias para la ficha y jornada proporcionadas');
        }

        foreach ($asistencias as $asistencia) {
            $asistencia->update([
                'hora_salida' => Carbon::now()->format('H:i:s'),
            ]);
        }

        return back()->with('success', 'Hora de salida actualizada exitosamente.');
    }

    public function setNewExitAsistenceWeb(Request $request)
    {
        $data = $request->all();

        $request->validate([
            'identificacion' => 'required|string|max:255',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'novedad' => 'required|string|max:255',
        ]);

        $fechaEjecucion = Carbon::now()->format('Y-m-d');

        $asistencia = AsistenciaAprendiz::where('numero_identificacion', $data['identificacion'])
            ->whereDate('created_at', $fechaEjecucion)
            ->first();

        if (! $asistencia) {
            return back()->with('error', 'No se encontró asistencia con los datos proporcionados.');
        }

        $asistencia->update([
            'hora_salida' => Carbon::now()->format('H:i:s'),
            'novedad_salida' => $data['novedad'],
        ]);

        return back()->with('success', 'Novedad de salida actualizada exitosamente.');
    }

    public function setNewEntranceAsistenceWeb(Request $request)
    {
        $data = $request->all();
        $request->validate([
            'identificacion' => 'required|string|max:255',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'novedad' => 'required|string|max:255',
        ]);

        $fechaEjecucion = Carbon::now()->format('Y-m-d');

        $asistencia = AsistenciaAprendiz::where('numero_identificacion', $data['identificacion'])
            ->whereDate('created_at', $fechaEjecucion)
            ->first();

        if (! $asistencia) {
            return back()->with('error', 'No se encontró asistencia con los datos proporcionados.');
        }

        $asistencia->update([
            'novedad_entrada' => $data['novedad'],
        ]);

        return back()->with('success', 'Novedad de entrada actualizada exitosamente.');
    }
}
