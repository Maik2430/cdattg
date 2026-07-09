<?php

namespace App\Services\Concerns\Complementarios\EstadisticaComplementario;

use Exception;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HandlesEstadisticaComplementarioExportActions
{
    /**
     * Exportar programas con mayor demanda a Excel
     */
    public function exportarProgramasDemandaExcel(): StreamedResponse
    {
        try {
            $estadisticas = $this->obtenerEstadisticasReales();
            $programasDemanda = $estadisticas['programas_demanda'];

            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'PROGRAMAS CON MAYOR DEMANDA');
            $sheet->mergeCells('A1:E1');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '007BFF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $sheet->setCellValue('A2', 'Fecha de generación: '.now()->format('d/m/Y H:i:s'));
            $sheet->mergeCells('A2:E2');
            $sheet->getStyle('A2')->applyFromArray([
                'font' => ['size' => 10, 'italic' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            $encabezados = [
                'A4' => 'Nombre del Programa',
                'B4' => 'Total Aspirantes',
                'C4' => 'Aceptados',
                'D4' => 'Pendientes',
                'E4' => 'Tasa de Aceptación (%)',
            ];

            foreach ($encabezados as $celda => $titulo) {
                $sheet->setCellValue($celda, $titulo);
            }

            $sheet->getStyle('A4:E4')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '6C757D'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $fila = 5;
            foreach ($programasDemanda as $programa) {
                $sheet->setCellValue('A'.$fila, $programa['programa']);
                $sheet->setCellValue('B'.$fila, $programa['total_aspirantes']);
                $sheet->setCellValue('C'.$fila, $programa['aceptados']);
                $sheet->setCellValue('D'.$fila, $programa['pendientes']);
                $sheet->setCellValue('E'.$fila, $programa['tasa_aceptacion']);

                $sheet->getStyle('B'.$fila.':E'.$fila)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                $fila++;
            }

            foreach (range('A', 'E') as $columna) {
                $sheet->getColumnDimension($columna)->setAutoSize(true);
            }

            $ultimaFila = $fila - 1;
            $sheet->getStyle('A4:E'.$ultimaFila)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ]);

            $fileName = 'programas_mayor_demanda_'.now()->format('Y-m-d_H-i-s').'.xlsx';

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

            Log::info('Archivo Excel de programas con mayor demanda generado', [
                'archivo' => $fileName,
                'registros' => count($programasDemanda),
                'user_id' => auth()->id(),
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Error exportando programas con mayor demanda a Excel', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'exception' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
