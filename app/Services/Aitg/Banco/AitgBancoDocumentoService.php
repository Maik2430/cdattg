<?php

namespace App\Services\Aitg\Banco;

use App\Models\Aitg\Banco\DocumentoBanco;
use App\Models\Aitg\Banco\SolicitudBanco;
use App\Models\Aitg\Banco\TipoArchivo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AitgBancoDocumentoService
{
    private const STORAGE_FOLDER = 'aitg_banco_instructores';

    public function subir(SolicitudBanco $solicitud, TipoArchivo $tipo, UploadedFile $archivo, User $user): DocumentoBanco
    {
        $nombreAlmacenado = $this->generarNombre($solicitud, $tipo, $archivo);
        $disk = $this->disk();
        $path = Storage::disk($disk)->putFileAs(self::STORAGE_FOLDER, $archivo, $nombreAlmacenado);

        $existente = DocumentoBanco::where('solicitud_id', $solicitud->id)
            ->where('tipo_archivo_id', $tipo->id)
            ->first();

        if ($existente) {
            $this->eliminarArchivoFisico($existente);
            $existente->update([
                'storage_disk' => $disk,
                'storage_path' => $path,
                'nombre_original' => $archivo->getClientOriginalName(),
                'nombre_almacenado' => $nombreAlmacenado,
                'mime_type' => $archivo->getMimeType(),
                'tamano_bytes' => $archivo->getSize(),
                'estado' => 'pendiente',
                'user_update_id' => $user->id,
            ]);

            return $existente->fresh(['tipoArchivo', 'validaciones.motivoRechazo']);
        }

        return DocumentoBanco::create([
            'solicitud_id' => $solicitud->id,
            'tipo_archivo_id' => $tipo->id,
            'storage_disk' => $disk,
            'storage_path' => $path,
            'nombre_original' => $archivo->getClientOriginalName(),
            'nombre_almacenado' => $nombreAlmacenado,
            'mime_type' => $archivo->getMimeType(),
            'tamano_bytes' => $archivo->getSize(),
            'estado' => 'pendiente',
            'user_create_id' => $user->id,
            'user_update_id' => $user->id,
        ]);
    }

    public function eliminar(DocumentoBanco $documento): void
    {
        $this->eliminarArchivoFisico($documento);
        $documento->delete();
    }

    private function eliminarArchivoFisico(DocumentoBanco $documento): void
    {
        if ($documento->storage_path && Storage::disk($documento->storage_disk)->exists($documento->storage_path)) {
            Storage::disk($documento->storage_disk)->delete($documento->storage_path);
        }
    }

    private function generarNombre(SolicitudBanco $solicitud, TipoArchivo $tipo, UploadedFile $archivo): string
    {
        $ext = $archivo->getClientOriginalExtension();

        return Str::upper($tipo->codigo) . "_{$solicitud->user_id}_" . now()->format('YmdHis') . ".{$ext}";
    }

    private function disk(): string
    {
        return config('filesystems.aitg_banco_disk', 'public');
    }
}
