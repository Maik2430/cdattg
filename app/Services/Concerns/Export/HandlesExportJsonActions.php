<?php

namespace App\Services\Concerns\Export;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait HandlesExportJsonActions
{
    public function exportarJSON(Collection $datos, string $titulo = 'Reporte'): string
    {
        try {
            $filename = 'exports/'.$titulo.'_'.time().'.json';
            $path = storage_path('app/public/'.$filename);

            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            file_put_contents($path, json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return $filename;
        } catch (\Exception $e) {
            Log::error('Error generando JSON', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
