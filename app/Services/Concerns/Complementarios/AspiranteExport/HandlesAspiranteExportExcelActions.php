<?php

namespace App\Services\Concerns\Complementarios\AspiranteExport;

use App\Exceptions\AspirantesSinDocumentosException;
use App\Exceptions\DescargaDocumentosException;
use App\Exceptions\ProgramaNoEncontradoException;
use Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HandlesAspiranteExportExcelActions
{
    /**
     * Exportar aspirantes a Excel
     */
    public function exportarAspirantesExcel(int $complementarioId): StreamedResponse
    {
        try {
            $programa = $this->programaRepository->findWithRelations($complementarioId);
            if (! $programa) {
                throw new ProgramaNoEncontradoException('Programa no encontrado');
            }

            $aspirantes = $this->aspiranteRepository->findForExport($complementarioId);
            $spreadsheet = $this->crearHojaCalculo($aspirantes);

            $fileName = 'aspirantes_'.str_replace(' ', '_', $programa->nombre).'_'.
                now()->format('Y-m-d_H-i-s').'.xlsx';

            return $this->crearRespuestaDescarga($spreadsheet, $fileName);
        } catch (Exception $e) {
            Log::error('Error exportando aspirantes a Excel: '.$e->getMessage(), [
                'complementario_id' => $complementarioId,
                'user_id' => auth()->id(),
                'exception' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Descargar cédulas de aspirantes en PDF
     */
    public function descargarCedulas(int $complementarioId)
    {
        try {
            $programa = $this->programaRepository->findWithRelations($complementarioId);
            if (! $programa) {
                throw new ProgramaNoEncontradoException('Programa no encontrado');
            }

            $aspirantes = $this->aspiranteRepository->findByProgramaConDocumentos($complementarioId);

            if ($aspirantes->isEmpty()) {
                throw new AspirantesSinDocumentosException('No hay aspirantes con documentos de identidad para descargar.');
            }

            $tempDir = $this->documentoService->createTempDirectory();
            $pdf = $this->crearFpdi();

            $resultados = $this->aspiranteComplementarioService->procesarDescargaDocumentos($aspirantes, $pdf, $tempDir);

            if ($resultados['archivos_agregados'] === 0) {
                $this->documentoService->limpiarArchivosTemporales($resultados['archivos_temporales']);
                throw new DescargaDocumentosException('No se pudieron descargar los documentos. Verifique que los archivos existan en Google Drive.');
            }

            return $this->aspiranteComplementarioService->generarArchivoPDF(
                $programa,
                $pdf,
                $tempDir,
                $resultados['archivos_temporales']
            );
        } catch (Exception $e) {
            Log::error('Error descargando cédulas: '.$e->getMessage(), [
                'complementario_id' => $complementarioId,
                'user_id' => auth()->id(),
                'exception' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
