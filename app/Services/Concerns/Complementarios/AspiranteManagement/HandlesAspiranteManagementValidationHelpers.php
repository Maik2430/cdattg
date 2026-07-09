<?php

namespace App\Services\Concerns\Complementarios\AspiranteManagement;

use Illuminate\Support\Facades\Gate;

trait HandlesAspiranteManagementValidationHelpers
{
    private function validarRechazarAspirante(int $complementarioId, int $aspiranteId): ?array
    {
        $errorResponse = null;

        if (! Gate::allows('ELIMINAR ASPIRANTE COMPLEMENTARIO')) {
            $errorResponse = [
                'success' => false,
                'message' => 'No tiene permisos para rechazar aspirantes.',
                'status_code' => 403,
            ];
        }

        if ($errorResponse === null) {
            $programa = $this->programaRepository->findWithRelations($complementarioId);
            if (! $programa) {
                $errorResponse = [
                    'success' => false,
                    'message' => self::PROGRAMA_NO_ENCONTRADO,
                    'status_code' => 200,
                ];
            }
        }

        if ($errorResponse === null) {
            $aspirantes = $this->aspiranteRepository->findByPrograma($complementarioId);
            $aspirante = $aspirantes->where('id', $aspiranteId)->first();

            if (! $aspirante) {
                $errorResponse = [
                    'success' => false,
                    'message' => 'Aspirante no encontrado.',
                    'status_code' => 200,
                ];
            }
        }

        return $errorResponse;
    }

    private function validarDocumentosPrecondiciones(int $complementarioId): ?array
    {
        $programa = $this->programaRepository->findWithRelations($complementarioId);
        if (! $programa) {
            return $this->createErrorResponse(self::PROGRAMA_NO_ENCONTRADO);
        }

        $errorResponse = null;
        $aspirantes = $this->aspiranteRepository->findByPrograma($complementarioId, ['persona.tipoDocumento']);
        if ($aspirantes->isEmpty()) {
            $errorResponse = $this->createErrorResponse('No hay aspirantes en este programa para validar documentos.');
        }

        return $errorResponse;
    }
}
