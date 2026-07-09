<?php

namespace App\Services\Concerns\Instructor;

use App\Models\Instructor;
use Illuminate\Database\Eloquent\Collection;

trait HandlesInstructorConsultaActions
{
    /**
     * Obtiene un instructor con sus relaciones
     */
    public function obtener(int $id): ?Instructor
    {
        return Instructor::with([
            'persona.tipoDocumento',
            'regional',
            'instructorFichas.ficha.programaFormacion',
        ])->find($id);
    }

    /**
     * Obtiene estadísticas de instructores
     *
     * @return array<string, int>
     */
    public function obtenerEstadisticas(): array
    {
        return [
            'total' => Instructor::count(),
            'activos' => Instructor::where('status', true)->count(),
            'inactivos' => Instructor::where('status', false)->count(),
            'con_fichas' => Instructor::whereHas('instructorFichas')->count(),
        ];
    }

    /**
     * Obtiene instructores para select/dropdown
     */
    public function obtenerParaSelect(?int $regionalId = null): Collection
    {
        $query = Instructor::with('persona')
            ->where('status', true);

        if ($regionalId) {
            $query->where('regional_id', $regionalId);
        }

        return $query->get()->map(function ($instructor) {
            return [
                'id' => $instructor->id,
                'nombre' => $instructor->persona->nombre_completo ?? 'Sin nombre',
                'regional_id' => $instructor->regional_id,
            ];
        });
    }
}
