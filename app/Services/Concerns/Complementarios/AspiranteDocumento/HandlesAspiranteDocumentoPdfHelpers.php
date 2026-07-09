<?php

namespace App\Services\Concerns\Complementarios\AspiranteDocumento;

trait HandlesAspiranteDocumentoPdfHelpers
{
    public function agregarPaginasAPDF($pdf, string $tempFilePath): void
    {
        $pageCount = $pdf->setSourceFile($tempFilePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
        }
    }

    public function limpiarArchivosTemporales(array $archivosTemporales): void
    {
        foreach ($archivosTemporales as $tempFile) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    public function createTempDirectory(): string
    {
        $tempDir = storage_path('app/temp');
        if (! file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        return $tempDir;
    }
}
