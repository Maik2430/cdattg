<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use App\Models\Aprendiz;
use App\Models\Asistencia;
use App\Models\AsistenciaAprendiz;
use App\Models\FichaCaracterizacion;
use App\Models\InstructorFichaCaracterizacion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesQrAsistenceFinalizeActions
{
    use HandlesQrAsistencePdfGeneration;

    public function finalizar_asistencia(Request $request)
    {
        try {
            $request->validate([
                'ficha_id' => 'required|integer',
                'caracterizacion_id' => 'required|integer',
                'asistencia_id' => 'required|integer',
                'observaciones' => 'nullable|string',
                'observaciones_aprendices' => 'nullable|array',
            ]);

            $fichaId = $request->input('ficha_id');
            $caracterizacionId = $request->input('caracterizacion_id');
            $asistenciaId = $request->input('asistencia_id');
            $observaciones = $request->input('observaciones');
            $observacionesAprendices = $request->input('observaciones_aprendices', []);

            $asistencia = Asistencia::find($asistenciaId);
            if (! $asistencia) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Asistencia no encontrada.',
                ], 404);
            }

            if ($observaciones !== null) {
                $asistencia->observaciones = $observaciones;
                $asistencia->save();
            }

            $this->guardarObservacionesAprendices($observacionesAprendices, $asistenciaId, $asistencia);

            $asistencia->finalizar();

            $fichaCaracterizacion = FichaCaracterizacion::with([
                'programaFormacion',
                'ambiente.piso.bloque.sede',
                'jornadaFormacion',
                'modalidadFormacion',
            ])->find($fichaId);

            if (! $fichaCaracterizacion) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ficha no encontrada.',
                ], 404);
            }

            $caracterizacion = InstructorFichaCaracterizacion::with([
                'instructor.persona',
            ])->find($caracterizacionId);

            $evidencia = $asistencia->evidencia;

            $todosLosAprendices = Aprendiz::with('persona')
                ->where('ficha_caracterizacion_id', $fichaId)
                ->get();

            $aprendicesConAsistencia = AsistenciaAprendiz::with('aprendiz.persona')
                ->where('asistencia_id', $asistenciaId)
                ->get();

            $aprendicesQueAsistieron = $aprendicesConAsistencia->pluck('aprendiz_ficha_id')->toArray();

            $asistieron = $todosLosAprendices->filter(
                fn ($aprendiz): bool => in_array($aprendiz->id, $aprendicesQueAsistieron)
            );

            $noAsistieron = $todosLosAprendices->filter(
                fn ($aprendiz): bool => ! in_array($aprendiz->id, $aprendicesQueAsistieron)
            );

            $evidencia->update(['id_estado' => 27]);

            $pdfData = $this->generarPdfAsistencia(
                $fichaCaracterizacion,
                $caracterizacion,
                $evidencia,
                $asistieron,
                $noAsistieron,
                $todosLosAprendices,
                $asistenciaId
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Asistencia finalizada correctamente',
                'pdf_url' => $pdfData['url'],
                'asistieron_count' => $asistieron->count(),
                'no_asistieron_count' => $noAsistieron->count(),
                'redirect_url' => route('asistence.web'),
            ]);
        } catch (Exception $e) {
            Log::error('Error al finalizar asistencia: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error al finalizar la asistencia: '.$e->getMessage(),
            ], 500);
        }
    }

    private function guardarObservacionesAprendices(array $observacionesAprendices, int $asistenciaId, Asistencia $asistencia): void
    {
        foreach ($observacionesAprendices as $aprendizId => $obsText) {
            $aprendiz = Aprendiz::find($aprendizId);
            if (! $aprendiz) {
                continue;
            }

            $asistenciaAprendiz = AsistenciaAprendiz::where('asistencia_id', $asistenciaId)
                ->where('aprendiz_ficha_id', $aprendizId)
                ->first();

            if ($asistenciaAprendiz) {
                $asistenciaAprendiz->observaciones = $obsText;
                $asistenciaAprendiz->save();
            } else {
                AsistenciaAprendiz::create([
                    'asistencia_id' => $asistenciaId,
                    'instructor_ficha_id' => $asistencia->instructor_ficha_id,
                    'aprendiz_ficha_id' => $aprendizId,
                    'hora_ingreso' => null,
                    'observaciones' => $obsText,
                    'user_create_id' => auth()->id(),
                ]);
            }
        }
    }
}
