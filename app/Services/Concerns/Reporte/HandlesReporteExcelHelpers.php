<?php

namespace App\Services\Concerns\Reporte;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait HandlesReporteExcelHelpers
{
    protected function generarExcel(array $datos): string
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'REPORTE DE ASISTENCIAS');
        $sheet->setCellValue('A2', 'Ficha: '.$datos['ficha']['numero']);
        $sheet->setCellValue('A3', 'Programa: '.$datos['ficha']['programa']);
        $sheet->setCellValue('A4', 'Periodo: '.$datos['periodo']['inicio'].' - '.$datos['periodo']['fin']);

        $row = 6;
        $sheet->setCellValue('A'.$row, 'Documento');
        $sheet->setCellValue('B'.$row, 'Nombre');
        $sheet->setCellValue('C'.$row, 'Hora Ingreso');
        $sheet->setCellValue('D'.$row, 'Hora Salida');
        $sheet->setCellValue('E'.$row, 'Novedad Entrada');
        $sheet->setCellValue('F'.$row, 'Novedad Salida');

        $row++;
        foreach ($datos['asistencias'] as $asistencia) {
            $sheet->setCellValue('A'.$row, $asistencia->numero_identificacion);
            $sheet->setCellValue('B'.$row, $asistencia->nombres.' '.$asistencia->apellidos);
            $sheet->setCellValue('C'.$row, $asistencia->hora_ingreso);
            $sheet->setCellValue('D'.$row, $asistencia->hora_salida);
            $sheet->setCellValue('E'.$row, $asistencia->novedad_entrada);
            $sheet->setCellValue('F'.$row, $asistencia->novedad_salida);
            $row++;
        }

        $filename = 'reportes/asistencias_'.time().'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $path = storage_path('app/public/'.$filename);
        $writer->save($path);

        return $filename;
    }

    protected function generarExcelAprendices(array $datos): string
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LISTADO DE APRENDICES');
        $sheet->setCellValue('A2', 'Ficha: '.$datos['ficha']['numero']);

        $row = 4;
        $sheet->setCellValue('A'.$row, 'Documento');
        $sheet->setCellValue('B'.$row, 'Nombre');
        $sheet->setCellValue('C'.$row, 'Email');
        $sheet->setCellValue('D'.$row, 'Estado');

        $row++;
        foreach ($datos['aprendices'] as $aprendiz) {
            $sheet->setCellValue('A'.$row, $aprendiz['documento']);
            $sheet->setCellValue('B'.$row, $aprendiz['nombre']);
            $sheet->setCellValue('C'.$row, $aprendiz['email']);
            $sheet->setCellValue('D'.$row, $aprendiz['estado']);
            $row++;
        }

        $filename = 'reportes/aprendices_'.time().'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $path = storage_path('app/public/'.$filename);
        $writer->save($path);

        return $filename;
    }
}
