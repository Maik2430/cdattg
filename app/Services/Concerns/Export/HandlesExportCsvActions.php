<?php

namespace App\Services\Concerns\Export;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

trait HandlesExportCsvActions
{
    public function exportarCSV(Collection $datos, array $columnas, string $titulo = 'Reporte'): string
    {
        try {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            $col = 'A';
            foreach ($columnas as $columna) {
                $sheet->setCellValue($col.'1', $columna['label']);
                $col++;
            }

            $row = 2;
            foreach ($datos as $dato) {
                $col = 'A';
                foreach ($columnas as $columna) {
                    $valor = is_array($dato) ? ($dato[$columna['field']] ?? '') : ($dato->{$columna['field']} ?? '');
                    $sheet->setCellValue($col.$row, $valor);
                    $col++;
                }
                $row++;
            }

            $filename = 'exports/'.$titulo.'_'.time().'.csv';
            $path = storage_path('app/public/'.$filename);

            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $writer = new Csv($spreadsheet);
            $writer->setDelimiter(';');
            $writer->save($path);

            return $filename;
        } catch (\Exception $e) {
            Log::error('Error generando CSV', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
