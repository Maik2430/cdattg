<?php

namespace App\Services\Concerns\Import;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

trait HandlesImportInstructoresActions
{
    public function importarInstructoresCSV(string $archivoPath): array
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
        $header = array_map('strtoupper', $header);

        $procesados = 0;
        $errores = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                if (count($row) != count($header)) {
                    $errores[] = $row;

                    continue;
                }

                $data = array_combine($header, $row);

                try {
                    $persona = Persona::create([
                        'tipo_documento' => 8,
                        'numero_documento' => $data['ID_PERSONAL'],
                        'primer_nombre' => $data['TITLE'],
                        'genero' => 11,
                        'email' => $data['CORREO INSTITUCIONAL'],
                    ]);

                    $user = User::create([
                        'email' => $data['CORREO INSTITUCIONAL'],
                        'password' => Hash::make($data['ID_PERSONAL']),
                        'persona_id' => $persona->id,
                    ]);

                    $user->assignRole('INSTRUCTOR');
                    $user->sendEmailVerificationNotification();

                    \App\Models\Instructor::create([
                        'persona_id' => $persona->id,
                        'regional_id' => 1,
                    ]);

                    $procesados++;
                } catch (\Exception $e) {
                    $errores[] = $data;
                    Log::error('Error importando fila', [
                        'data' => $data,
                        'error' => $e->getMessage(),
                    ]);

                    continue;
                }
            }

            DB::commit();

            Log::info('Importación completada', [
                'procesados' => $procesados,
                'errores' => count($errores),
            ]);

            return [
                'exitoso' => true,
                'procesados' => $procesados,
                'errores' => $errores,
                'total' => count($rows),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error en importación masiva', [
                'error' => $e->getMessage(),
            ]);

            return [
                'exitoso' => false,
                'mensaje' => $e->getMessage(),
            ];
        }
    }
}
