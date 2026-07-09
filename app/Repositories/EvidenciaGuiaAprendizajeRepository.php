<?php

namespace App\Repositories;

use App\Models\EvidenciaGuiaAprendizaje;
use Illuminate\Database\Eloquent\Collection;

class EvidenciaGuiaAprendizajeRepository
{
    /**
     * Obtiene evidencias por guía
     */
    public function obtenerPorGuia(int $guiaId): Collection
    {
        return EvidenciaGuiaAprendizaje::where('guia_aprendizaje_id', $guiaId)
            ->with(['evidencia', 'aprendiz.persona'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtiene evidencias por aprendiz
     */
    public function obtenerPorAprendiz(int $aprendizId): Collection
    {
        return EvidenciaGuiaAprendizaje::where('aprendiz_id', $aprendizId)
            ->with(['guiaAprendizaje', 'evidencia'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Crea evidencia de guía
     */
    public function crear(array $datos): EvidenciaGuiaAprendizaje
    {
        return EvidenciaGuiaAprendizaje::create($datos);
    }

    /**
     * Califica evidencia
     */
    public function calificar(int $id, float $calificacion, ?string $observaciones = null): bool
    {
        return EvidenciaGuiaAprendizaje::where('id', $id)->update([
            'calificacion' => $calificacion,
            'observaciones' => $observaciones,
            'fecha_calificacion' => now(),
        ]);
    }
}
