<?php

namespace App\Services\Concerns\Complementarios\InscripcionComplementario;

use App\Exceptions\ProcesarDocumentoIdentidadException;
use App\Models\Complementarios\AspiranteComplementario;
use App\Models\Persona;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesInscripcionComplementarioPersonaHelpers
{
    private function verificarInscripcionExistente(int $programaId): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return $this->aspiranteRepository->existeInscripcion(Auth::user()->persona_id, $programaId);
    }

    private function procesarPersona(array $data): Persona
    {
        return $this->personaRepository->createOrUpdate($data);
    }

    private function procesarUsuario(array $data, Persona $persona): void
    {
        $this->userService->createOrUpdateForAspirante($data, $persona);
    }

    private function crearAspirante(Persona $persona, int $programaId, array $data): AspiranteComplementario
    {
        return $this->aspiranteRepository->create([
            'persona_id' => $persona->id,
            'complementario_id' => $programaId,
            'observaciones' => $data['observaciones'] ?? null,
            'estado' => 1,
        ]);
    }

    private function procesarDocumento(array $data, AspiranteComplementario $aspirante, Persona $persona): void
    {
        if (! isset($data['documento_identidad'])) {
            return;
        }

        try {
            $upload = $this->documentoService->subirDocumentoIdentidad(
                $persona,
                $data['documento_identidad']
            );

            $this->aspiranteRepository->update($aspirante, [
                'documento_identidad_path' => $upload['path'],
                'documento_identidad_nombre' => $upload['name'],
            ]);

        } catch (Exception $e) {
            Log::error('Error al procesar documento: '.$e->getMessage(), [
                'aspirante_id' => $aspirante->id,
                'exception' => $e->getTraceAsString(),
            ]);

            $this->aspiranteRepository->update($aspirante, ['estado' => 1]);

            throw new ProcesarDocumentoIdentidadException('Error al procesar el documento de identidad');
        }
    }
}
