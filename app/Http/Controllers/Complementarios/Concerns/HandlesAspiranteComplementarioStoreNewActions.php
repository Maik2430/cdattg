<?php

namespace App\Http\Controllers\Complementarios\Concerns;

use App\Http\Requests\Complementarios\CreateAspiranteRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesAspiranteComplementarioStoreNewActions
{
    use HandlesAspiranteComplementarioPersonaCreation;

    public function storeNewAspirante(CreateAspiranteRequest $request, int $programa)
    {
        $resultado = $this->procesarCreacionAspirante($request, $programa);

        return $this->formatearRespuesta($request, $resultado, $programa);
    }

    /** @return array<string, mixed> */
    private function procesarCreacionAspirante(CreateAspiranteRequest $request, int $programa): array
    {
        try {
            $validated = $request->validated();

            $errorValidacion = $this->validarProgramaYPersona($programa, $validated['numero_documento']);
            if ($errorValidacion !== null) {
                return $errorValidacion;
            }

            $persona = $this->crearPersonaDesdeValidacion($validated);

            $observaciones = $validated['observaciones'] ?? 'Creado desde gestión de aspirantes';
            $resultado = $this->aspiranteManagementService->agregarAspirante(
                $programa,
                $persona->numero_documento,
                $observaciones
            );

            $aspirante = $resultado['aspirante'] ?? null;
            if ($aspirante && isset($validated['documento_identidad'])) {
                $this->aspiranteManagementService->almacenarDocumentoIdentidad(
                    $aspirante,
                    $persona,
                    $validated['documento_identidad']
                );
            }

            $statusCode = $resultado['status_code'] ?? 200;
            unset($resultado['status_code']);
            $resultado['status_code'] = $statusCode;

            return $resultado;
        } catch (Exception $e) {
            Log::error('Error en storeNewAspirante() al crear aspirante: '.$e->getMessage(), [
                'programa' => $programa,
                'numero_documento' => $request->validated()['numero_documento'] ?? null,
                'user_id' => Auth::id(),
                'exception' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => self::ERROR_MENSAJE_SERVIDOR,
                'status_code' => 500,
            ];
        }
    }

    /** @return array<string, mixed>|null */
    private function validarProgramaYPersona(int $programa, string $numeroDocumento): ?array
    {
        $programaModel = $this->programaRepository->findWithRelations($programa);
        if (! $programaModel) {
            return [
                'success' => false,
                'message' => 'Programa no encontrado.',
                'status_code' => 200,
            ];
        }

        $personaExistente = $this->personaService->buscarPorDocumento($numeroDocumento);
        if ($personaExistente) {
            return [
                'success' => false,
                'message' => 'Ya existe una persona con este número de documento. Use "Agregar Aspirante" en lugar de "Crear Nuevo".',
                'status_code' => 200,
            ];
        }

        return null;
    }

    /** @param array<string, mixed> $resultado
     * @return RedirectResponse|JsonResponse
     */
    private function formatearRespuesta(Request $request, array $resultado, int $programa)
    {
        $statusCode = $resultado['status_code'] ?? 200;
        $success = $resultado['success'] ?? false;
        $message = $resultado['message'] ?? '';
        unset($resultado['status_code']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($resultado, $statusCode);
        }

        if ($success) {
            return redirect()
                ->route('aspirantes.programa', ['programa' => $programa])
                ->with('success', $message ?: 'Aspirante creado exitosamente.');
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', $message ?: 'Error al crear el aspirante.');
    }
}
