<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use App\Models\Evidencias;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\RegistroActividades;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesQrAsistenceActivityActions
{
    public function agregar_actividad(Request $request)
    {
        $request->validate([
            'ficha_id' => 'required|exists:ficha_caracterizacions,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha' => 'required|date',
        ]);

        try {
            $actividad = new RegistroActividades;
            $actividad->ficha_id = $request->input('ficha_id');
            $actividad->titulo = $request->input('titulo');
            $actividad->descripcion = $request->input('descripcion');
            $actividad->fecha = $request->input('fecha');
            $actividad->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Actividad agregada correctamente.',
                'actividad' => $actividad,
            ], 201);
        } catch (Exception $e) {
            Log::error('Error al agregar actividad: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo agregar la actividad.',
            ], 500);
        }
    }

    public function terminar_actividad(Request $request)
    {
        try {
            Evidencias::terminarActividad($request->input('evidencia_id'));
            $caracterizacion = InstructorFichaCaracterizacion::findOrFail($request->input('caracterizacion'));

            return redirect()->route('registro-actividades.index', $caracterizacion)->with('success', 'Actividad terminada correctamente.');
        } catch (Exception $e) {
            Log::error('Error al terminar actividad: '.$e->getMessage());

            return redirect()->back()->with('error', 'No se pudo terminar la actividad.');
        }
    }
}
