<?php

namespace App\Http\Controllers\Concerns\ProgramaFormacion;

use App\Models\ProgramaFormacion;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesProgramaFormacionCompetenciasActions
{
    public function detachCompetencia(string $programaId, string $competenciaId): RedirectResponse
    {
        try {
            $programa = ProgramaFormacion::findOrFail($programaId);

            if (! $programa->competencias()->where('competencias.id', $competenciaId)->exists()) {
                return redirect()->back()->with('warning', 'La competencia seleccionada no está asociada al programa.');
            }

            $programa->competencias()->detach($competenciaId);

            Log::info('Competencia desasociada de programa', [
                'programa_id' => $programaId,
                'competencia_id' => $competenciaId,
                'usuario_id' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'La competencia fue desasociada del programa.');
        } catch (Exception $e) {
            Log::error('Error al desasociar competencia de programa', [
                'programa_id' => $programaId,
                'competencia_id' => $competenciaId,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'No fue posible desasociar la competencia.');
        }
    }
}
