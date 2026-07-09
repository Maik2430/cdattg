<?php

namespace App\Http\Controllers\Concerns\Complementarios\DocumentoComplementario;

use App\Models\Complementarios\AspiranteComplementario;
use App\Models\Parametro;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HandlesDocumentoComplementarioCrudWriteActions
{
    /**
     * Procesar la subida de documentos
     */
    public function subirDocumento(Request $request, $id)
    {
        Log::info('=== subirDocumento method reached ===', [
            'request_data' => $request->all(),
            'files' => $request->files->all(),
            'aspirante_id' => $request->aspirante_id,
            'has_file' => $request->hasFile('documento_identidad'),
            'file_info' => $request->hasFile('documento_identidad') ? [
                'name' => $request->file('documento_identidad')->getClientOriginalName(),
                'size' => $request->file('documento_identidad')->getSize(),
                'mime' => $request->file('documento_identidad')->getMimeType(),
            ] : null,
        ]);

        // Validar el archivo
        Log::info('Antes de validación - campos recibidos:', $request->all());
        $request->validate([
            'documento_identidad' => 'required|file|mimes:pdf|max:5120', // 5MB máximo
            'aspirante_id' => 'required|exists:aspirantes_complementarios,id',
            'acepto_privacidad' => 'required',
        ]);
        Log::info('Después de validación - validación pasó');

        try {
            // Obtener el aspirante
            Log::info('Buscando aspirante con ID: '.$request->aspirante_id);
            $aspirante = AspiranteComplementario::findOrFail($request->aspirante_id);

            Log::info('Aspirante found', [
                'aspirante_id' => $aspirante->id,
                'persona_id' => $aspirante->persona_id,
                'numero_documento' => $aspirante->persona->numero_documento,
            ]);

            // Procesar el archivo y subirlo a Google Drive
            if ($request->hasFile('documento_identidad')) {
                $file = $request->file('documento_identidad');

                // Crear nombre de archivo con formato:
                // tipo_documento_NumeroDocumento_timestamp.pdf
                $tipoDocumento = $aspirante->persona->tipoDocumento?->name ?? 'DOC';
                $numeroDocumento = $aspirante->persona->numero_documento;
                $timestamp = now()->format('d-m-y-H-i-s');

                // Reemplazar espacios por guiones bajos para consistencia
                $tipoDocumento = str_replace(' ', '_', $tipoDocumento);

                $fileName = "{$tipoDocumento}_{$numeroDocumento}_{$timestamp}.{$file->getClientOriginalExtension()}";

                Log::info('Attempting to upload file to Google Drive', [
                    'file_name' => $fileName,
                    'file_size' => $file->getSize(),
                    'disk_config' => config('filesystems.disks.google'),
                    'google_credentials_path' => storage_path('app/google-credentials.json'),
                    'credentials_exist' => file_exists(storage_path('app/google-credentials.json')),
                ]);

                // Verificar configuración de Google Drive
                $notSetMessage = 'NOT SET';
                $googleDisk = config('filesystems.disks.google', []);
                Log::info('Google Drive config check', [
                    'client_id' => ! empty($googleDisk['clientId']) ? 'SET' : $notSetMessage,
                    'client_secret' => ! empty($googleDisk['clientSecret']) ? 'SET' : $notSetMessage,
                    'refresh_token' => ! empty($googleDisk['refreshToken']) ? 'SET' : $notSetMessage,
                    'folder_id' => ! empty($googleDisk['folderId']) ? 'SET' : $notSetMessage,
                ]);

                // Subir a Google Drive
                $path = Storage::disk('google')->putFileAs('documentos_aspirantes', $file, $fileName);

                Log::info('File uploaded successfully', ['path' => $path]);

                // Actualizar el registro del aspirante con la información del documento
                $aspirante->update([
                    'documento_identidad_path' => $path,
                    'documento_identidad_nombre' => $fileName,
                    'estado' => 1, // Estado "En proceso" - se mantiene hasta que se evalúe
                ]);
            }

            Log::info('DocumentoComplementarioController - Documento subido exitosamente, redirigiendo a login', [
                'aspirante_id' => $aspirante->id,
                'persona_id' => $aspirante->persona_id,
                'email' => $aspirante->persona->email,
            ]);

            return redirect()->route('login.index')->with(
                'success',
                'Documento subido exitosamente. Su cuenta de usuario ha sido creada. '.
                'Puede iniciar sesión con su correo electrónico y número de documento como contraseña.'
            );

        } catch (Exception $e) {
            Log::error('Error al subir documento: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Error al subir el documento. Por favor intente nuevamente.');
        }
    }

    /**
     * Procesar envío de documento desde el formulario
     */
    public function procesarDocumentoSubmit(Request $request)
    {
        Log::info('=== procesarDocumentoSubmit method reached ===', [
            'request_data' => $request->all(),
            'files' => $request->files->all(),
        ]);

        // Validar los datos del formulario
        $request->validate([
            'tipo_documento' => 'required|exists:parametros,id',
            'numero_documento' => 'required|string|max:50',
            'documento_identidad' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB máximo
        ]);

        try {
            // Procesar el archivo y subirlo a Google Drive
            if ($request->hasFile('documento_identidad')) {
                $file = $request->file('documento_identidad');

                // Obtener el nombre del tipo de documento
                $tipoDocumento = Parametro::find($request->tipo_documento);
                $tipoDocumentoName = $tipoDocumento ? str_replace(' ', '_', $tipoDocumento->name) : 'DOC';
                $numeroDocumento = $request->numero_documento;
                $timestamp = now()->format('d-m-y-H-i-s');

                // Crear nombre de archivo
                $fileName = "{$tipoDocumentoName}_{$numeroDocumento}_{$timestamp}.{$file->getClientOriginalExtension()}";

                Log::info('Attempting to upload file to Google Drive', [
                    'file_name' => $fileName,
                    'tipo_documento' => $tipoDocumentoName,
                    'numero_documento' => $numeroDocumento,
                    'file_size' => $file->getSize(),
                ]);

                // Subir a Google Drive
                $path = Storage::disk('google')->putFileAs('documentos_aspirantes', $file, $fileName);

                Log::info('File uploaded successfully', ['path' => $path]);

                return redirect()->route('procesar-documentos')
                    ->with('success', 'Documento subido exitosamente a Google Drive.');
            }

            return back()->with('error', 'No se pudo procesar el archivo.');

        } catch (Exception $e) {
            Log::error('Error al procesar documento: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Error al procesar el documento. Por favor intente nuevamente.');
        }
    }
}
