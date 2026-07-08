<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use App\Models\Asistencia;
use App\Models\Evidencias;
use App\Models\InstructorFichaCaracterizacion;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesQrAsistenceCaracterSelection
{
    public function caracterSelected(InstructorFichaCaracterizacion $caracterizacion, $asistencia_id = null)
    {
        try {
            Log::info('=== DEBUG CARACTERSELECTED ===');
            Log::info('Caracterizacion ID desde route: '.$caracterizacion->id);
            Log::info('Caracterizacion tipo: '.get_class($caracterizacion));

            if (! $asistencia_id) {
                $asistencia_id = request()->query('asistencia_id');
            }

            Log::info('Asistencia ID (route/query): '.($asistencia_id ?? 'NULL'));

            $asistencia = $this->resolverAsistenciaParaCaracterizacion($caracterizacion, $asistencia_id);
            if ($asistencia instanceof RedirectResponse) {
                return $asistencia;
            }

            $evidencia = $asistencia->evidencia()->first();

            Log::info('Evidencia encontrada/creada: '.($evidencia ? 'SI' : 'NO'));
            if ($evidencia) {
                Log::info('Evidencia ID: '.$evidencia->id);
            }

            Log::info('Ficha ID a buscar en servicio: '.$caracterizacion->ficha_id);
            $datosCaracterizacion = $this->asistenceQrService->obtenerDatosCaracterizacion(
                $caracterizacion->ficha_id,
                Auth::user(),
                $asistencia->id
            );

            if (! $datosCaracterizacion['fichaCaracterizacion']) {
                return redirect()->back()->with('error', 'Ficha de caracterización no encontrada.');
            }

            Log::info('Pasando a la vista - asistencia ID: '.($asistencia ? $asistencia->id : 'NULL'));
            Log::info('Pasando a la vista - asistencia está finalizada: '.($asistencia ? ($asistencia->is_finished ? 'SI' : 'NO') : 'SIN ASISTENCIA'));

            return view('qr_asistence.index', [
                'caracterizacion' => $caracterizacion,
                'fichaCaracterizacion' => $datosCaracterizacion['fichaCaracterizacion'],
                'aprendizPersonaConAsistencia' => $datosCaracterizacion['aprendices'],
                'horarioHoy' => $datosCaracterizacion['horarioHoy'],
                'asistencia' => $asistencia,
                'evidencia' => $evidencia,
            ]);
        } catch (Exception $e) {
            Log::error('Error en caracterSelected - ERROR COMPLETO:');
            Log::error('Mensaje: '.$e->getMessage());
            Log::error('Archivo: '.$e->getFile());
            Log::error('Línea: '.$e->getLine());
            Log::error('Trace: '.$e->getTraceAsString());
            Log::error('Caracterizacion ID: '.($caracterizacion->id ?? 'NULL'));
            Log::error('=== FIN ERROR COMPLETO ===');

            return redirect()->back()->with('error', 'Error al cargar caracterización. Revisa el log para más detalles.');
        }
    }

    private function resolverAsistenciaParaCaracterizacion(
        InstructorFichaCaracterizacion $caracterizacion,
        mixed $asistencia_id
    ): Asistencia|RedirectResponse {
        if (! $asistencia_id) {
            Log::info('Buscando asistencia activa para ficha_id: '.$caracterizacion->ficha_id);

            $asistencia = Asistencia::deFicha($caracterizacion->ficha_id)
                ->activa()
                ->first();

            Log::info('Resultado búsqueda asistencia activa: '.($asistencia ? 'ENCONTRADA ID: '.$asistencia->id : 'NO ENCONTRADA'));

            if (! $asistencia) {
                Log::info('Creando nueva evidencia y asistencia...');

                $evidencia = Evidencias::create([
                    'nombre' => 'Evidencia por defecto',
                    'id_estado' => 1,
                    'fecha_evidencia' => now(),
                    'user_create_id' => Auth::id(),
                    'user_edit_id' => Auth::id(),
                ]);

                Log::info('Evidencia creada: '.$evidencia->id);

                $asistencia = Asistencia::create([
                    'evidencia_id' => $evidencia->id,
                    'instructor_ficha_id' => $caracterizacion->ficha_id,
                    'fecha' => now()->toDateString(),
                    'hora_inicio' => now(),
                    'is_finished' => false,
                    'user_create_id' => Auth::id(),
                    'user_edit_id' => Auth::id(),
                ]);

                Log::info('Nueva asistencia creada: '.$asistencia->id);
            } else {
                Log::info('Asistencia activa encontrada: '.$asistencia->id);
            }

            return $asistencia;
        }

        $asistencia = Asistencia::find($asistencia_id);

        if (! $asistencia) {
            return redirect()->back()->with('error', 'Asistencia no encontrada.');
        }

        Log::info('Asistencia específica encontrada: '.$asistencia->id);

        return $asistencia;
    }
}
