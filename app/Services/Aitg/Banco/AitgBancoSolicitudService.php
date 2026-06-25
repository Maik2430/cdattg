<?php

namespace App\Services\Aitg\Banco;

use App\Models\Aitg\Banco\DocumentoBanco;
use App\Models\Aitg\Banco\SolicitudBanco;
use App\Models\Aitg\Banco\TipoArchivo;
use App\Models\Aitg\Banco\ValidacionDocumento;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AitgBancoSolicitudService
{
    public function obtenerOCrear(User $user): SolicitudBanco
    {
        return SolicitudBanco::firstOrCreate(
            ['user_id' => $user->id],
            [
                'persona_id' => $user->persona?->id,
                'estado' => 'borrador',
                'user_create_id' => $user->id,
                'user_update_id' => $user->id,
            ]
        );
    }

    public function puedeEnviarRevision(SolicitudBanco $solicitud): bool
    {
        $obligatorios = TipoArchivo::activos()->where('es_obligatorio', true)->pluck('id');
        if ($obligatorios->isEmpty()) {
            return $solicitud->documentos()->exists();
        }

        $subidos = $solicitud->documentos()->whereIn('tipo_archivo_id', $obligatorios)->pluck('tipo_archivo_id');

        return $obligatorios->diff($subidos)->isEmpty();
    }

    public function enviarRevision(SolicitudBanco $solicitud, User $user): SolicitudBanco
    {
        $solicitud->update([
            'estado' => 'pendiente_revision',
            'fecha_envio' => now(),
            'user_update_id' => $user->id,
        ]);

        $solicitud->documentos()
            ->whereIn('estado', ['pendiente', 'rechazado'])
            ->update(['estado' => 'en_revision', 'user_update_id' => $user->id]);

        return $solicitud->fresh(['documentos.tipoArchivo', 'documentos.validaciones.motivoRechazo']);
    }

    public function validarDocumento(
        DocumentoBanco $documento,
        User $validador,
        string $resultado,
        ?int $motivoRechazoId,
        ?string $descripcion
    ): DocumentoBanco {
        DB::transaction(function () use ($documento, $validador, $resultado, $motivoRechazoId, $descripcion) {
            ValidacionDocumento::create([
                'documento_id' => $documento->id,
                'validador_user_id' => $validador->id,
                'resultado' => $resultado,
                'motivo_rechazo_id' => $resultado === 'rechazado' ? $motivoRechazoId : null,
                'descripcion' => $descripcion,
                'fecha_validacion' => now(),
            ]);

            $documento->update([
                'estado' => $resultado === 'aprobado' ? 'aprobado' : 'rechazado',
                'user_update_id' => $validador->id,
            ]);

            $this->actualizarEstadoSolicitud($documento->solicitud()->firstOrFail(), $validador);
        });

        return $documento->fresh(['tipoArchivo', 'validaciones.motivoRechazo', 'solicitud']);
    }

    private function actualizarEstadoSolicitud(SolicitudBanco $solicitud, User $validador): void
    {
        $documentos = $solicitud->documentos()->get();
        if ($documentos->isEmpty()) {
            return;
        }

        if ($documentos->contains(fn ($d) => $d->estado === 'rechazado')) {
            $solicitud->update([
                'estado' => 'requiere_correccion',
                'user_update_id' => $validador->id,
            ]);

            return;
        }

        if ($documentos->contains(fn ($d) => in_array($d->estado, ['pendiente', 'en_revision'], true))) {
            return;
        }

        if ($documentos->every(fn ($d) => $d->estado === 'aprobado')) {
            $solicitud->update([
                'estado' => 'aprobado',
                'fecha_resolucion' => now(),
                'user_update_id' => $validador->id,
            ]);

            $usuario = $solicitud->user;
            if ($usuario && ! $usuario->hasRole('ASPIRANTE INSTRUCTOR')) {
                $rol = Role::firstOrCreate(['name' => 'ASPIRANTE INSTRUCTOR']);
                $usuario->assignRole($rol);
            }
        }
    }
}
