<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario\Concerns;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait HandlesProductoExportActions
{
    public function exportarPdf()
    {
        $productos = $this->repository->obtenerTodosOrdenadosPorCantidadDesc();

        $pdf = Pdf::loadView('inventario.productos.report', [
            'productos' => $productos,
            'pdf' => true,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('reporte_productos_inventario.pdf');
    }

    public function exportarExcel(): BinaryFileResponse
    {
        $productos = $this->repository->obtenerTodosOrdenadosPorCantidadDesc();

        $datos = $productos->map(static function ($producto): array {
            return [
                'id' => $producto->id,
                'nombre' => $producto->name,
                'codigo_barras' => $producto->codigo_barras,
                'cantidad' => $producto->cantidad,
                'categoria' => $producto->categoria->name ?? '',
                'marca' => $producto->marca->name ?? '',
                'ubicacion' => $producto->ambiente->title ?? '',
                'fecha_vencimiento' => optional($producto->fecha_vencimiento)->format('d/m/Y') ?? '',
                'fecha_registro' => optional($producto->created_at)->format('d/m/Y') ?? '',
                'estado' => $producto->estado?->parametro?->name ?? '',
            ];
        });

        $columnas = [
            ['field' => 'id', 'label' => 'ID'],
            ['field' => 'nombre', 'label' => 'Producto'],
            ['field' => 'codigo_barras', 'label' => 'Código de barras'],
            ['field' => 'cantidad', 'label' => 'Cantidad'],
            ['field' => 'categoria', 'label' => 'Categoría'],
            ['field' => 'marca', 'label' => 'Marca'],
            ['field' => 'ubicacion', 'label' => 'Ubicación'],
            ['field' => 'fecha_vencimiento', 'label' => 'F. Vencimiento'],
            ['field' => 'fecha_registro', 'label' => 'F. Registro'],
            ['field' => 'estado', 'label' => 'Estado'],
        ];

        $relativePath = $this->exportService->exportarExcel($datos, $columnas, 'productos_inventario');
        $absolutePath = storage_path('app/public/'.$relativePath);

        return response()->download($absolutePath, 'productos_inventario.xlsx')->deleteFileAfterSend(true);
    }
}
