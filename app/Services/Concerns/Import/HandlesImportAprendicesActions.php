<?php

namespace App\Services\Concerns\Import;

use App\Models\Persona;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesImportAprendicesActions
{
    public function importarAprendicesCSV(string $archivoPath, int $fichaId): array
    {
        $csvData = file_get_contents($archivoPath);

        if (substr($csvData, 0, 3) === "\u{FEFF}") {
            $csvData = substr($csvData, 3);
        }

        $rows = array_map(function ($row) {
            return str_getcsv($row, ';');
        }, explode("\n", $csvData));

        $header = array_shift($rows);
        $header = array_map('trim', $header);

        $procesados = 0;
        $errores = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                if (empty(array_filter($row))) {
                    continue;
                }

                if (count($row) != count($header)) {
                    $errores[] = $row;

                    continue;
                }

                $data = array_combine($header, $row);

                try {
                    $persona = Persona::create([
                        'tipo_documento' => $data['tipo_documento'] ?? 1,
                        'numero_documento' => $data['numero_documento'],
                        'primer_nombre' => $data['primer_nombre'],
                        'segundo_nombre' => $data['segundo_nombre'] ?? null,
                        'primer_apellido' => $data['primer_apellido'],
                        'segundo_apellido' => $data['segundo_apellido'] ?? null,
                        'email' => $data['email'],
                        'genero' => $data['genero'] ?? 11,
                    ]);

                    $this->aprendizRepo->crear([
                        'persona_id' => $persona->id,
                        'ficha_caracterizacion_id' => $fichaId,
                        'estado' => true,
                    ]);

                    $procesados++;
                } catch (\Exception $e) {
                    $errores[] = $data;
                    Log::error('Error importando aprendiz', [
                        'data' => $data,
                        'error' => $e->getMessage(),
                    ]);

                    continue;
                }
            }

            DB::commit();

            return [
                'exitoso' => true,
                'procesados' => $procesados,
                'errores' => $errores,
                'total' => count($rows),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'exitoso' => false,
                'mensaje' => $e->getMessage(),
            ];
        }
    }
}
