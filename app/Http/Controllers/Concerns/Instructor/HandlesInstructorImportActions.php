<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\Persona;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

trait HandlesInstructorImportActions
{
    public function createImportarCSV()
    {
        return view('Instructores.createImportarCSV');
    }

    public function storeImportarCSV(Request $request)
    {
        try {
            // Validar que el archivo subido sea un archivo CSV o TXT
            $request->validate([
                'archivoCSV' => 'required|file|mimes:csv,txt|max:'.self::MAX_IMPORT_FILE_KB,
            ]);

            // Obtener el archivo subido
            $archivo = $request->file('archivoCSV');
            $csvData = file_get_contents($archivo);

            // Eliminar el BOM (Byte Order Mark) si está presente al inicio del archivo
            if (substr($csvData, 0, 3) === "\u{FEFF}") {
                $csvData = substr($csvData, 3);
            }

            // Convertir el contenido del archivo CSV en un array de filas, usando punto y coma como delimitador
            $rows = array_map(function ($row) {
                return str_getcsv($row, ';');
            }, explode("\n", $csvData));

            // Extraer el encabezado (primera fila) del CSV
            $header = array_shift($rows);

            // Eliminar espacios en blanco alrededor de cada elemento del encabezado y convertirlo a mayúsculas
            $header = array_map('trim', $header);
            $header = array_map('strtoupper', $header);

            // Definir el encabezado esperado
            $expectedHeader = ['TITLE', 'ID_PERSONAL', self::CSV_HEADER_EMAIL];

            // Comprobar si el encabezado del CSV coincide con el encabezado esperado
            if ($header !== $expectedHeader) {
                return redirect()
                    ->back()
                    ->with('error', 'El encabezado del archivo CSV no coincide con el formato esperado.');
            }

            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            $errores = [];
            $procesados = 0;

            // Procesar cada fila de datos del CSV
            foreach ($rows as $row) {
                // Verificar que la cantidad de columnas en la fila
                // coincida con la cantidad de columnas en el encabezado
                if (count($row) != count($header)) {
                    $errores[] = $row;

                    continue;
                }

                // Combinar el encabezado con los datos de la fila
                $data = array_combine($header, $row);

                try {
                    // Crear una nueva entrada en la tabla `Persona`
                    $persona = Persona::create([
                        'tipo_documento' => 8,
                        'numero_documento' => $data['ID_PERSONAL'],
                        'primer_nombre' => $data['TITLE'],
                        'genero' => 11,
                        'email' => $data[self::CSV_HEADER_EMAIL],
                    ]);

                    // Crear una nueva entrada en la tabla `User`
                    $user = User::create([
                        'email' => $data[self::CSV_HEADER_EMAIL],
                        'password' => Hash::make($data['ID_PERSONAL']),
                        'persona_id' => $persona->id,
                    ]);

                    // Asignar el rol de 'INSTRUCTOR' al usuario
                    $user->assignRole('INSTRUCTOR');

                    // Enviar email de verificación automáticamente
                    $user->sendEmailVerificationNotification();

                    // Crear una nueva entrada en la tabla `Instructor`
                    Instructor::create([
                        'persona_id' => $persona->id,
                        'regional_id' => 1,
                    ]);

                    // Incrementar el contador de registros procesados con éxito
                    $procesados++;
                } catch (Exception $e) {
                    // Si ocurre un error, agregar la fila a la lista de errores y continuar con la siguiente
                    $errores[] = $data;

                    continue;
                }
            }

            // Confirmar la transacción de base de datos
            DB::commit();

            // Preparar el mensaje de éxito
            $mensaje = 'Instructores creados exitosamente: '.$procesados;

            // Si hubo errores, agregar una nota al mensaje de éxito
            if (! empty($errores)) {
                $mensaje .= '. Algunos registros no pudieron ser procesados.';
            }

            // Mostrar la vista con los errores y el mensaje de éxito
            return view('Instructores.errorImport', compact('errores'))
                ->with('success', $mensaje);
        } catch (QueryException $e) {
            // Si ocurre un error en la base de datos, revertir la transacción
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Error en la base de datos: '.$e->getMessage());
        } catch (Exception $e) {
            // Si ocurre un error general, revertir la transacción
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Error: '.$e->getMessage());
        }
    }
}
