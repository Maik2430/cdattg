<?php

namespace App\Http\Controllers\Complementarios\Concerns;

use App\Http\Requests\Complementarios\StoreProgramaComplementarioRequest;
use App\Http\Requests\Complementarios\UpdateProgramaComplementarioRequest;
use App\Models\Complementarios\ComplementarioCatalogo;
use App\Models\Complementarios\ComplementarioOfertado;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesProgramaComplementarioWriteActions
{
    public function store(StoreProgramaComplementarioRequest $request): RedirectResponse
    {
        $payload = $request->validated();

        DB::transaction(function () use ($payload): void {
            $atributos = $this->extractProgramaAtributos($payload);
            $programa = ComplementarioOfertado::create($atributos);

            $this->complementarioService->sincronizarDiasFormacion($programa, $payload['dias'] ?? null);
            $this->sincronizarEstructuraAcademica($programa, $payload);
        });

        return redirect()->route('complementarios-ofertados.index')
            ->with('success', 'Programa creado exitosamente.');
    }

    public function update(UpdateProgramaComplementarioRequest $request, ComplementarioOfertado $programa): RedirectResponse
    {
        $payload = $request->validated();

        DB::transaction(function () use ($programa, $payload): void {
            $atributos = $this->extractProgramaAtributos($payload);
            $programa->update($atributos);

            $this->complementarioService->sincronizarDiasFormacion($programa, $payload['dias'] ?? null);
            $this->sincronizarEstructuraAcademica($programa, $payload);
        });

        return redirect()->route('complementarios-ofertados.show', $programa->id)
            ->with('success', 'Programa actualizado exitosamente.');
    }

    public function destroy(ComplementarioOfertado $programa): JsonResponse
    {
        $relacionesActivas = $this->obtenerRelacionesActivas($programa);
        if (! empty($relacionesActivas)) {
            $mensaje = $this->construirMensajeErrorRelaciones($relacionesActivas);

            return response()->json(['success' => false, 'message' => $mensaje], 422);
        }

        try {
            $programa->delete();

            return response()->json(['success' => true, 'message' => 'Programa eliminado exitosamente.']);
        } catch (Exception $e) {
            return $this->manejarExcepcion($e);
        }
    }

    /** @return array<string, mixed> */
    private function extractProgramaAtributos(array $payload): array
    {
        $atributos = collect($payload)->only([
            'catalogo_id',
            'codigo',
            'justificacion',
            'cupos',
            'jornada_id',
            'ambiente_id',
            'ambiente_comentario',
        ])->toArray();

        if (! empty($payload['catalogo_id'])) {
            $catalogo = ComplementarioCatalogo::query()->find($payload['catalogo_id']);
            if ($catalogo !== null) {
                $atributos['catalogo_id'] = $catalogo->id;
                $atributos['codigo'] = $catalogo->prf_codigo;
            }
        }

        if (isset($payload['estado'])) {
            $estadoId = $this->complementarioService->convertirEstadoLegacyAEstadoId((int) $payload['estado']);
            if ($estadoId) {
                $atributos['estado_id'] = $estadoId;
            }
        }

        return $atributos;
    }

    private function sincronizarEstructuraAcademica(ComplementarioOfertado $programa, array $payload): void
    {
        if (isset($payload['competencias'])) {
            $programa->competencias()->sync($payload['competencias']);
        }
        if (isset($payload['raps'])) {
            $programa->raps()->sync($payload['raps']);
        }
        if (isset($payload['guias'])) {
            $programa->guiasAprendizaje()->sync($payload['guias']);
        }
    }

    /** @return list<string> */
    private function obtenerRelacionesActivas(ComplementarioOfertado $programa): array
    {
        $relaciones = [];

        if ($programa->aspirantes()->exists()) {
            $relaciones[] = 'aspirantes inscritos';
        }
        if ($programa->competencias()->exists()) {
            $relaciones[] = 'competencias asociadas';
        }
        if ($programa->raps()->exists()) {
            $relaciones[] = 'resultados de aprendizaje (RAPs) asociados';
        }
        if ($programa->guiasAprendizaje()->exists()) {
            $relaciones[] = 'guías de aprendizaje asociadas';
        }
        if ($programa->diasFormacion()->exists()) {
            $relaciones[] = 'días de formación asignados';
        }

        return $relaciones;
    }

    /** @param list<string> $relaciones */
    private function construirMensajeErrorRelaciones(array $relaciones): string
    {
        $mensaje = 'No se puede eliminar el programa porque tiene ' . implode(', ', $relaciones) . '. ';

        return $mensaje . 'Por favor, elimine estas relaciones primero o cambie el estado del programa a "Sin Oferta".';
    }

    private function manejarExcepcion(Exception $e): JsonResponse
    {
        if ($e instanceof QueryException && $e->getCode() == 23000) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el programa porque tiene registros relacionados en el sistema. Por favor, elimine primero todas las relaciones (aspirantes, competencias, RAPs, guías de aprendizaje, días de formación) o cambie el estado del programa a "Sin Oferta".',
            ], 422);
        }

        Log::error('Error al eliminar programa complementario', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString(),
            'exception_type' => get_class($e),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Ocurrió un error al intentar eliminar el programa. Por favor, intente nuevamente.',
        ], 500);
    }
}
