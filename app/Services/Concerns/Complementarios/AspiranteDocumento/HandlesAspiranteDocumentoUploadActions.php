<?php

namespace App\Services\Concerns\Complementarios\AspiranteDocumento;

use App\Models\Persona;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HandlesAspiranteDocumentoUploadActions
{
    public function subirDocumentoIdentidad(Persona $persona, UploadedFile $archivo): array
    {
        $fileName = $this->generarNombreArchivo($persona, $archivo);

        Log::info('Subiendo archivo a Google Drive', [
            'file_name' => $fileName,
            'numero_documento' => $persona->numero_documento,
            'persona_id' => $persona->id,
        ]);

        $path = Storage::disk('google')->putFileAs('documentos_aspirantes', $archivo, $fileName);

        Log::info('Documento procesado exitosamente', [
            'persona_id' => $persona->id,
            'path' => $path,
        ]);

        return [
            'path' => $path,
            'name' => $fileName,
        ];
    }

    private function generarNombreArchivo(Persona $persona, UploadedFile $archivo): string
    {
        $tipoDocumento = $persona->tipoDocumento !== null
            ? str_replace(' ', '_', $persona->tipoDocumento->name)
            : 'DOC';

        $tipoDocumento = str_replace(' ', '_', $tipoDocumento);
        $numeroDocumento = $persona->numero_documento;
        $timestamp = now()->format('d-m-y-H-i-s');
        $extension = $archivo->getClientOriginalExtension();

        return "{$tipoDocumento}_{$numeroDocumento}_{$timestamp}.{$extension}";
    }
}
