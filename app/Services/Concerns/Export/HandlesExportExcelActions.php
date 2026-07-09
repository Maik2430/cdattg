<?php

namespace App\Services\Concerns\Export;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait HandlesExportExcelActions
{
    public function exportarExcel(Collection $datos, array $columnas, string $titulo = 'Reporte'): string
    {
        try {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            $primerCampo = $columnas[0]['field'] ?? null;
            $inicioTituloCol = (is_string($primerCampo) && strtolower($primerCampo) === 'id') ? 'B' : 'A';

            $sheet->setCellValue($inicioTituloCol.'1', strtoupper($titulo));
            $sheet->mergeCells($inicioTituloCol.'1:'.chr(64 + count($columnas)).'1');
            $sheet->getStyle($inicioTituloCol.'1')->getFont()->setBold(true)->setSize(14);

            $sheet->setCellValue($inicioTituloCol.'2', 'Fecha de generación: '.now()->format('d/m/Y H:i:s'));

            $headerRow = 4;
            $col = 'A';
            foreach ($columnas as $columna) {
                $sheet->setCellValue($col.$headerRow, $columna['label']);
                $col++;
            }

            $lastColumnLetter = chr(64 + count($columnas));
            $headerRange = 'A'.$headerRow.':'.$lastColumnLetter.$headerRow;

            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            $sheet->getRowDimension($headerRow)->setRowHeight(20);

            $dataStartRow = 5;
            $row = $dataStartRow;
            foreach ($datos as $dato) {
                $col = 'A';
                foreach ($columnas as $columna) {
                    $valor = is_array($dato) ? ($dato[$columna['field']] ?? '') : ($dato->{$columna['field']} ?? '');
                    $sheet->setCellValue($col.$row, $valor);
                    $col++;
                }
                $row++;
            }

            $lastDataRow = $row - 1;

            if ($lastDataRow >= $dataStartRow) {
                for ($currentRow = $dataStartRow; $currentRow <= $lastDataRow; $currentRow++) {
                    if ($currentRow % 2 === 0) {
                        $range = 'A'.$currentRow.':'.$lastColumnLetter.$currentRow;
                        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID);
                        $sheet->getStyle($range)->getFill()->getStartColor()->setRGB('E9EDF5');
                    }
                }
            }

            $sheet->setAutoFilter('A'.$headerRow.':'.$lastColumnLetter.$headerRow);

            foreach (range('A', $lastColumnLetter) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'exports/'.$titulo.'_'.time().'.xlsx';
            $path = storage_path('app/public/'.$filename);

            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save($path);

            Log::info('Archivo Excel generado', [
                'archivo' => $filename,
                'registros' => $datos->count(),
            ]);

            return $filename;
        } catch (\Exception $e) {
            Log::error('Error generando Excel', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
