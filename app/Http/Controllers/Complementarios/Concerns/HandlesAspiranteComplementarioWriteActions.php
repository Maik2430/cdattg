<?php

namespace App\Http\Controllers\Complementarios\Concerns;

use App\Http\Requests\Complementarios\RechazarAspiranteRequest;
use App\Http\Requests\Complementarios\StoreAspiranteRequest;
use App\Http\Requests\Complementarios\UpdateAspiranteRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesAspiranteComplementarioWriteActions
{
    public function destroy(RechazarAspiranteRequest $request, int $programa, int $aspirante): JsonResponse
    {
        $validated = $request->validated();
        $motivoRechazo = $validated['motivo_rechazo'] ?? null;
        $observaciones = $validated['observaciones'] ?? null;

        $resultado = $this->aspiranteManagementService->rechazarAspirante(
            $programa,
            $aspirante,
            $motivoRechazo,
            $observaciones
        );

        $statusCode = $resultado['status_code'] ?? 200;
        unset($resultado['status_code']);

        return response()->json($resultado, $statusCode);
    }

    public function store(StoreAspiranteRequest $request, ?int $programa = null): JsonResponse
    {
        try {
            $programaId = $programa ?? $request->route('complementarioId') ?? $request->route('programa');

            if ($programaId === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID del programa no proporcionado.',
                ], 400);
            }

            $validated = $request->validated();
            $numeroDocumento = $validated['numero_documento'];
            $observaciones = $validated['observaciones'] ?? null;

            $resultado = $this->aspiranteManagementService->agregarAspirante(
                $programaId,
                $numeroDocumento,
                $observaciones
            );

            $statusCode = $resultado['status_code'] ?? 200;
            unset($resultado['status_code']);

            return response()->json($resultado, $statusCode);
        } catch (Exception $e) {
            Log::error('Error en store() al agregar aspirante: '.$e->getMessage(), [
                'programa' => $programaId ?? null,
                'numero_documento' => $request->validated()['numero_documento'] ?? null,
                'user_id' => Auth::id(),
                'exception' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => self::ERROR_MENSAJE_SERVIDOR,
            ], 500);
        }
    }

    public function update(UpdateAspiranteRequest $request, int $programa, int $aspirante): JsonResponse
    {
        try {
            $validated = $request->validated();

            $aspiranteModel = $this->aspiranteRepository->findByPrograma($programa)
                ->where('id', $aspirante)
                ->first();

            if (! $aspiranteModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aspirante no encontrado en este programa.',
                ], 200);
            }

            $updateData = [];
            if (isset($validated['estado'])) {
                $updateData['estado'] = $validated['estado'];
            }
            if (isset($validated['observaciones'])) {
                $updateData['observaciones'] = $validated['observaciones'];
            }

            if (! empty($updateData)) {
                $this->aspiranteRepository->update($aspiranteModel, $updateData);

                Log::info('Aspirante actualizado exitosamente', [
                    'aspirante_id' => $aspirante,
                    'complementario_id' => $programa,
                    'user_id' => Auth::id(),
                    'updated_fields' => array_keys($updateData),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Aspirante actualizado exitosamente.',
            ], 200);
        } catch (Exception $e) {
            Log::error('Error en update() al actualizar aspirante: '.$e->getMessage(), [
                'programa' => $programa,
                'aspirante' => $aspirante,
                'user_id' => Auth::id(),
                'exception' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => self::ERROR_MENSAJE_SERVIDOR,
            ], 500);
        }
    }

    public function validarDocumentos(int $complementarioId): JsonResponse
    {
        $resultado = $this->aspiranteManagementService->validarDocumentos($complementarioId, $this->documentoService);

        $statusCode = $resultado['status_code'] ?? 200;
        unset($resultado['status_code']);

        return response()->json($resultado, $statusCode);
    }
}
