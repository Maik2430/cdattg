<?php

namespace App\Services;

use App\Models\GuiasAprendizaje;
use App\Models\ResultadosAprendizaje;
use App\Repositories\EvidenciaGuiaAprendizajeRepository;
use App\Repositories\EvidenciasRepository;
use App\Repositories\GuiasAprendizajeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GuiaAprendizajeService
{
    protected GuiasAprendizajeRepository $guiasRepo;

    protected EvidenciasRepository $evidenciasRepo;

    protected EvidenciaGuiaAprendizajeRepository $evidenciaGuiaRepo;

    public function __construct(
        GuiasAprendizajeRepository $guiasRepo,
        EvidenciasRepository $evidenciasRepo,
        EvidenciaGuiaAprendizajeRepository $evidenciaGuiaRepo
    ) {
        $this->guiasRepo = $guiasRepo;
        $this->evidenciasRepo = $evidenciasRepo;
        $this->evidenciaGuiaRepo = $evidenciaGuiaRepo;
    }

    /**
     * Obtiene guías por programa
     */
    public function obtenerPorPrograma(int $programaId): Collection
    {
        return $this->guiasRepo->obtenerPorPrograma($programaId);
    }

    /**
     * Registra evidencia de aprendiz en guía
     */
    public function registrarEvidencia(int $guiaId, int $aprendizId, array $datosEvidencia): bool
    {
        return DB::transaction(function () use ($guiaId, $aprendizId, $datosEvidencia) {
            $evidencia = $this->evidenciaGuiaRepo->crear([
                'guia_aprendizaje_id' => $guiaId,
                'aprendiz_id' => $aprendizId,
                'evidencia_id' => $datosEvidencia['evidencia_id'] ?? null,
                'descripcion' => $datosEvidencia['descripcion'] ?? null,
                'archivo' => $datosEvidencia['archivo'] ?? null,
            ]);

            Log::info('Evidencia registrada', [
                'guia_id' => $guiaId,
                'aprendiz_id' => $aprendizId,
                'evidencia_id' => $evidencia->id,
            ]);

            return true;
        });
    }

    /**
     * Califica evidencia de aprendiz
     */
    public function calificarEvidencia(int $evidenciaGuiaId, float $calificacion, ?string $observaciones = null): bool
    {
        return DB::transaction(function () use ($evidenciaGuiaId, $calificacion, $observaciones) {
            $calificado = $this->evidenciaGuiaRepo->calificar($evidenciaGuiaId, $calificacion, $observaciones);

            if ($calificado) {
                Log::info('Evidencia calificada', [
                    'evidencia_guia_id' => $evidenciaGuiaId,
                    'calificacion' => $calificacion,
                ]);
            }

            return $calificado;
        });
    }

    /**
     * Obtiene progreso de aprendiz en guías
     */
    public function obtenerProgresoAprendiz(int $aprendizId): array
    {
        $evidencias = $this->evidenciaGuiaRepo->obtenerPorAprendiz($aprendizId);

        $total = $evidencias->count();
        $calificadas = $evidencias->whereNotNull('calificacion')->count();
        $aprobadas = $evidencias->where('calificacion', '>=', 3.0)->count();

        return [
            'total_evidencias' => $total,
            'evidencias_calificadas' => $calificadas,
            'evidencias_aprobadas' => $aprobadas,
            'porcentaje_completado' => $total > 0 ? round(($calificadas / $total) * 100, 2) : 0,
            'porcentaje_aprobacion' => $calificadas > 0 ? round(($aprobadas / $calificadas) * 100, 2) : 0,
        ];
    }

    public function resultadosTienenCompetenciasDistintas(GuiasAprendizaje $guiaAprendizaje, int $resultadoId): bool
    {
        $resultadosExistentes = $guiaAprendizaje->resultadosAprendizaje()->with('competencias')->get();

        if ($resultadosExistentes->isEmpty()) {
            return false;
        }

        $resultadoExistente = $resultadosExistentes->first();
        if (! $resultadoExistente instanceof ResultadosAprendizaje) {
            return false;
        }

        $competenciaExistente = $resultadoExistente->competencias()->first();
        $nuevoResultado = ResultadosAprendizaje::with('competencias')->find($resultadoId);

        if (! $nuevoResultado instanceof ResultadosAprendizaje) {
            return false;
        }

        $competenciaNueva = $nuevoResultado->competencias()->first();

        return $competenciaExistente && $competenciaNueva
            && $competenciaExistente->id !== $competenciaNueva->id;
    }
}
