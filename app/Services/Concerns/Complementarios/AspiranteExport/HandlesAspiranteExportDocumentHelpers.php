<?php

namespace App\Services\Concerns\Complementarios\AspiranteExport;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use setasign\Fpdi\Fpdi;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HandlesAspiranteExportDocumentHelpers
{
    /**
     * Convertir tipo de documento a iniciales (CC, TI, etc.)
     */
    private function convertirTipoDocumentoAIniciales($tipoDocumento): string
    {
        $tipoDocumento = $this->limpiarTexto($tipoDocumento);

        $mapeo = [
            'cedula de ciudadania' => 'CC',
            'cedula de extranjeria' => 'CE',
            'tarjeta de identidad' => 'TI',
            'pasaporte' => 'PA',
            'registro civil' => 'RC',
            'cedula' => 'CC',
            'extranjeria' => 'CE',
            'tarjeta identidad' => 'TI',
        ];

        $tipoDocumentoLower = strtolower($tipoDocumento);
        if (isset($mapeo[$tipoDocumentoLower])) {
            return $mapeo[$tipoDocumentoLower];
        }

        foreach ($mapeo as $nombre => $iniciales) {
            if (strpos($tipoDocumentoLower, $nombre) !== false) {
                return $iniciales;
            }
        }

        return strtoupper(substr($tipoDocumento, 0, 2));
    }

    /**
     * Limpiar texto quitando acentos y caracteres especiales
     */
    private function limpiarTexto($texto): string
    {
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto);
        $texto = preg_replace('/[^a-zA-Z0-9\s]/', '', $texto);

        return trim($texto);
    }

    /**
     * Crear instancia de Fpdi
     * Método protegido para facilitar el testing
     */
    protected function crearFpdi(): Fpdi
    {
        return new Fpdi;
    }

    /**
     * Crear respuesta de descarga
     */
    private function crearRespuestaDescarga(Spreadsheet $spreadsheet, string $fileName): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set(
            'Content-Type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$fileName.'"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
