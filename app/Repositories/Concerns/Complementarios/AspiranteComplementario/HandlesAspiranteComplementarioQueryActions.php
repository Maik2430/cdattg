<?php

namespace App\Repositories\Concerns\Complementarios\AspiranteComplementario;

use App\Models\Complementarios\AspiranteComplementario;
use Illuminate\Database\Eloquent\Collection;

trait HandlesAspiranteComplementarioQueryActions
{
    public function findByPrograma(int $programaId, array $relations = ['persona', 'complementario']): Collection
    {
        return AspiranteComplementario::with($relations)
            ->where('complementario_id', $programaId)
            ->get();
    }

    public function findByProgramaConDocumentos(int $programaId): Collection
    {
        return AspiranteComplementario::with(['persona.tipoDocumento'])
            ->where('complementario_id', $programaId)
            ->whereHas('persona', function ($query): void {
                $query->where('condocumento', 1);
            })
            ->get()
            ->sortBy(function ($aspirante) {
                return $aspirante->persona->numero_documento;
            });
    }

    public function findByProgramaConDocumentosExcluyendoRechazados(int $programaId): Collection
    {
        return AspiranteComplementario::with(['persona.tipoDocumento'])
            ->where('complementario_id', $programaId)
            ->where('estado', '!=', 4)
            ->whereHas('persona', function ($query): void {
                $query->where('condocumento', 1);
            })
            ->get()
            ->sortBy(function ($aspirante) {
                return $aspirante->persona->numero_documento;
            });
    }

    public function findByProgramaParaExportacion(int $programaId): Collection
    {
        return AspiranteComplementario::with(['persona.tipoDocumento', 'persona.parametroCaracterizacion'])
            ->where('complementario_id', $programaId)
            ->where('estado', '!=', 4)
            ->whereHas('persona', function ($query): void {
                $query->where('condocumento', 1)
                    ->where('estado_sofia', '!=', 277);
            })
            ->get()
            ->sortBy(function ($aspirante) {
                return $aspirante->persona->numero_documento;
            });
    }

    public function countByEstado(int $programaId, int $estado): int
    {
        return AspiranteComplementario::where('complementario_id', $programaId)
            ->where('estado', $estado)
            ->count();
    }

    public function countByPrograma(int $programaId): int
    {
        return AspiranteComplementario::where('complementario_id', $programaId)->count();
    }

    public function existeInscripcion(int $personaId, int $programaId): bool
    {
        return AspiranteComplementario::where('persona_id', $personaId)
            ->where('complementario_id', $programaId)
            ->exists();
    }

    public function findByPersonaYPrograma(int $personaId, int $programaId): ?AspiranteComplementario
    {
        return AspiranteComplementario::where('persona_id', $personaId)
            ->where('complementario_id', $programaId)
            ->first();
    }

    public function findById(int $id): ?AspiranteComplementario
    {
        return AspiranteComplementario::find($id);
    }

    public function findForExport(int $programaId): Collection
    {
        return AspiranteComplementario::with(['persona.caracterizacion', 'persona.tipoDocumento'])
            ->where('complementario_id', $programaId)
            ->get();
    }
}
