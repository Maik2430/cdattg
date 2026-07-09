<?php

namespace App\Services\Concerns\Complementarios\AspiranteExport;

use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait HandlesAspiranteExportSpreadsheetHelpers
{
    /**
     * Crear hoja de cálculo con datos de aspirantes
     */
    private function crearHojaCalculo(Collection $aspirantes): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'FORMATO PARA LA INSCRIPCIÓN DE ASPIRANTES EN SOFIA PLUS v1.0');
        $sheet->mergeCells('A1:G1');

        $titleStyle = [
            'font' => [
                'bold' => false,
                'size' => 14,
                'color' => ['rgb' => self::COLOR_NEGRO_RGB],
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C4D79B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => self::COLOR_NEGRO_RGB],
                ],
            ],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($titleStyle);

        $this->establecerEncabezados($sheet);
        $this->llenarDatos($sheet, $aspirantes);

        $sheet->getRowDimension(1)->setRowHeight(15);
        $sheet->getRowDimension(2)->setRowHeight(45);

        $calibriStyle = [
            'font' => [
                'name' => 'Calibri',
                'size' => 11,
            ],
        ];
        $sheet->getStyle('A1:G'.$sheet->getHighestRow())->applyFromArray($calibriStyle);

        $dataStyle = [
            'font' => [
                'size' => 8,
            ],
            'alignment' => [
                'wrapText' => true,
            ],
        ];
        $sheet->getStyle('A2:G'.$sheet->getHighestRow())->applyFromArray($dataStyle);

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('G')->setWidth(10);

        return $spreadsheet;
    }

    /**
     * Establecer encabezados de la hoja
     */
    private function establecerEncabezados(Worksheet $sheet): void
    {
        $sheet->setCellValue('A2', 'Resultado del Registro (Reservado para el sistema)');
        $sheet->setCellValue('B2', 'Tipo de Identificación');
        $sheet->setCellValue('C2', 'Número de Identificación');
        $sheet->setCellValue('D2', 'Código de la ficha');
        $sheet->setCellValue('E2', 'Tipo Población Aspirante');
        $sheet->setCellValue('F2', '');
        $sheet->setCellValue('G2', 'Codigo Empresa (Solo si la ficha es cerrada)');

        $headerStyle = [
            'font' => [
                'bold' => false,
                'color' => ['rgb' => self::COLOR_NEGRO_RGB],
                'name' => 'Calibri',
                'size' => 8,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => self::COLOR_NEGRO_RGB],
                ],
            ],
        ];
        $sheet->getStyle('A2:G2')->applyFromArray($headerStyle);
    }

    /**
     * Llenar datos en la hoja
     */
    private function llenarDatos(Worksheet $sheet, $aspirantes): void
    {
        $row = 3;
        foreach ($aspirantes as $aspirante) {
            $tipoDocumento = $aspirante->persona->tipoDocumento ? $aspirante->persona->tipoDocumento->name : 'N/A';
            $numeroDocumento = $aspirante->persona->numero_documento;

            $caracterizacion = $aspirante->persona->caracterizacion ?
                $aspirante->persona->caracterizacion->name : 'Sin caracterización';

            $tipoIdentificacion = $this->convertirTipoDocumentoAIniciales($tipoDocumento);

            $sheet->setCellValue('A'.$row, '');
            $sheet->setCellValue('B'.$row, $tipoIdentificacion);
            $sheet->setCellValue('C'.$row, $numeroDocumento);
            $sheet->setCellValue('D'.$row, '');
            $sheet->setCellValue('E'.$row, $caracterizacion);
            $sheet->setCellValue('F'.$row, '');
            $sheet->setCellValue('G'.$row, '');

            $row++;
        }
    }
}
