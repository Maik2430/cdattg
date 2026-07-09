<?php

namespace App\Services\Concerns\AsistenceQr;

use App\Models\Aprendiz;
use App\Models\AsistenciaAprendiz;
use App\Models\FichaCaracterizacion;
use App\Models\InstructorFichaCaracterizacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait HandlesAsistenceQrCaracterizacionActions
{
    public function obtenerDatosCaracterizacion(int $caracterizacionId, $user, ?int $asistenciaId = null): array
    {
        Log::info('=== DEBUG OBTENER DATOS CARACTERIZACION ===');
        Log::info('Caracterizacion ID: '.$caracterizacionId);
        Log::info('User ID: '.($user ? $user->id : 'NULL'));
        Log::info('Asistencia ID (filtro tabla): '.($asistenciaId ?? 'NULL'));

        $fichaCaracterizacion = FichaCaracterizacion::with([
            'diasFormacion.dia',
            'programaFormacion',
            'instructor.persona',
            'jornadaFormacion.parametro',
        ])->find($caracterizacionId);

        Log::info('FichaCaracterizacion encontrada: '.($fichaCaracterizacion ? 'SI' : 'NO'));
        if ($fichaCaracterizacion) {
            Log::info('FichaCaracterizacion ID: '.$fichaCaracterizacion->id);
            Log::info('FichaCaracterizacion ficha: '.($fichaCaracterizacion->ficha ?? 'NULL'));
        }

        if (! $fichaCaracterizacion) {
            Log::error('FichaCaracterizacion NO encontrada, retornando null');

            return [
                'fichaCaracterizacion' => null,
                'aprendices' => collect(),
                'horarioHoy' => null,
            ];
        }

        Log::info('=== FIN DEBUG OBTENER DATOS ===');

        $diaHoy = now()->dayOfWeek;
        $diaId = ($diaHoy == 0) ? 18 : $diaHoy + 11;

        $horarioHoy = null;
        if ($fichaCaracterizacion->diasFormacion) {
            $horarioHoy = $fichaCaracterizacion->diasFormacion
                ->where('dia_id', $diaId)
                ->first();

            if ($horarioHoy) {
                $horarioHoy->hora_inicio = Carbon::parse($horarioHoy->hora_inicio)->format('h:i A');
                $horarioHoy->hora_fin = Carbon::parse($horarioHoy->hora_fin)->format('h:i A');
            }
        }

        $instructorFichaId = null;
        if ($user && $user->persona && $user->persona->instructor) {
            $instructor = $user->persona->instructor;
            $instructorFicha = InstructorFichaCaracterizacion::where('instructor_id', $instructor->id)
                ->where('ficha_id', $fichaCaracterizacion->id)
                ->first();
            if ($instructorFicha) {
                $instructorFichaId = $instructorFicha->id;
            }
        }

        Log::info('Obteniendo aprendices de la ficha: '.$fichaCaracterizacion->id);
        $aprendicesFicha = Aprendiz::where('ficha_caracterizacion_id', $fichaCaracterizacion->id)->get();
        Log::info('Cantidad de aprendices encontrados: '.$aprendicesFicha->count());

        foreach ($aprendicesFicha as $index => $aprendiz) {
            Log::info("Aprendiz {$index}: ID={$aprendiz->id}, documento=".($aprendiz->persona->numero_documento ?? 'SIN PERSONA'));
        }

        $aprendizPersonaConAsistencia = collect();
        $fechaActual = Carbon::now()->format('Y-m-d');

        foreach ($aprendicesFicha as $aprendiz) {
            if ($aprendiz && $aprendiz->persona) {
                $persona = $aprendiz->persona;

                $asistenciaHoy = null;
                if ($instructorFichaId) {
                    $query = AsistenciaAprendiz::where('aprendiz_ficha_id', $aprendiz->id)
                        ->where('instructor_ficha_id', $instructorFichaId);

                    if ($asistenciaId) {
                        $query->where('asistencia_id', $asistenciaId);
                    } else {
                        $query->whereDate('created_at', $fechaActual);
                    }

                    $asistenciaHoy = $query->first();
                }

                $persona->asistenciaHoy = $asistenciaHoy;
                $persona->aprendiz_id = $aprendiz->id;

                if ($persona->asistenciaHoy) {
                    $persona->asistenciaHoy->formatted_hora_ingreso = Carbon::parse($persona->asistenciaHoy->hora_ingreso)->format('h:i A');
                    $persona->asistenciaHoy->formatted_hora_salida = $persona->asistenciaHoy->hora_salida
                        ? Carbon::parse($persona->asistenciaHoy->hora_salida)->format('h:i A')
                        : null;
                }

                $aprendizPersonaConAsistencia->push($persona);
            }
        }

        return [
            'fichaCaracterizacion' => $fichaCaracterizacion,
            'aprendices' => $aprendizPersonaConAsistencia,
            'horarioHoy' => $horarioHoy,
        ];
    }
}
