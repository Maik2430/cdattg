<?php

namespace App\Services\Concerns\Reporte;

trait HandlesReportePdfHelpers
{
    protected function generarPDF(array $datos): string
    {
        throw new \Exception('Generación de PDF no implementada aún');
    }

    protected function generarPDFAprendices(array $datos): string
    {
        throw new \Exception('Generación de PDF no implementada aún');
    }
}
