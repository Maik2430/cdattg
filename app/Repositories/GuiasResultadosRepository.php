<?php

namespace App\Repositories;

use App\Models\GuiasResultados;
use Illuminate\Database\Eloquent\Collection;

class GuiasResultadosRepository
{
    /**
     * Obtiene guías por resultado de aprendizaje
     */
    public function obtenerPorResultado(int $resultadoId): Collection
    {
        return GuiasResultados::where('resultado_aprendizaje_id', $resultadoId)
            ->with(['guiaAprendizaje'])
            ->get();
    }

    /**
     * Crea relación guía-resultado
     */
    public function crear(array $datos): GuiasResultados
    {
        return GuiasResultados::create($datos);
    }
}
