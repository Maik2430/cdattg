<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Log;

trait HandlesQrAsistencePdfGeneration
{
    private function generarPdfAsistencia($fichaCaracterizacion, $caracterizacion, $evidencia, $asistieron, $noAsistieron, $todosLosAprendices, int $asistenciaId)
    {
        try {
            $nombreArchivo = 'asistencia_'.$fichaCaracterizacion->ficha.'_'.date('Y-m-d_H-i-s').'.pdf';
            $rutaArchivo = 'asistencia_pdfs/'.$nombreArchivo;

            $directorio = public_path('asistencia_pdfs');
            if (! file_exists($directorio)) {
                mkdir($directorio, 0755, true);
            }

            $pdf = Pdf::loadView('pdf.asistencia_reporte', [
                'fichaCaracterizacion' => $fichaCaracterizacion,
                'caracterizacion' => $caracterizacion,
                'evidencia' => $evidencia,
                'asistenciaId' => $asistenciaId,
                'asistieron' => $asistieron,
                'noAsistieron' => $noAsistieron,
                'todosLosAprendices' => $todosLosAprendices,
                'fecha' => now()->format('d/m/Y'),
                'hora' => now()->format('h:i A'),
            ]);

            $pdf->save($rutaArchivo);

            return [
                'url' => asset('asistencia_pdfs/'.$nombreArchivo),
                'filename' => $nombreArchivo,
            ];
        } catch (Exception $e) {
            Log::error('Error al generar PDF: '.$e->getMessage());
            throw $e;
        }
    }
}
