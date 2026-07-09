<?php

namespace App\Services\Concerns\Complementarios\AspiranteComplementario;

trait HandlesAspiranteComplementarioReadActions
{
    /**
     * Obtener aspirantes con documentos
     */
    public function getAspirantesConDocumentos($complementarioId)
    {
        return $this->aspiranteRepository->findByProgramaConDocumentosExcluyendoRechazados($complementarioId);
    }

    /**
     * Obtener aspirantes válidos para exportación (excluye rechazados y sin documento)
     */
    public function getAspirantesParaExportacion($complementarioId)
    {
        return $this->aspiranteRepository->findByProgramaParaExportacion($complementarioId);
    }

    /**
     * Obtener estadísticas de exclusión para mostrar en modal
     */
    public function getEstadisticasExclusion($complementarioId)
    {
        return $this->aspiranteRepository->getEstadisticasExclusion($complementarioId);
    }
}
