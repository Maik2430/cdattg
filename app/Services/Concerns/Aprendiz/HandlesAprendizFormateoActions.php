<?php

namespace App\Services\Concerns\Aprendiz;

use App\Models\Aprendiz;

trait HandlesAprendizFormateoActions
{
    /**
     * Prepara datos para API/JSON
     */
    public function formatearParaApi(Aprendiz $aprendiz): array
    {
        return [
            'id' => $aprendiz->id,
            'persona_id' => $aprendiz->persona_id,
            'nombre_completo' => $aprendiz->persona->nombre_completo ?? 'N/A',
            'numero_documento' => $aprendiz->persona->numero_documento ?? 'N/A',
            'email' => $aprendiz->persona->email ?? 'N/A',
            'ficha' => $aprendiz->fichaCaracterizacion->ficha ?? 'N/A',
            'programa' => $aprendiz->fichaCaracterizacion->programaFormacion->nombre ?? 'N/A',
            'estado' => $aprendiz->estado,
        ];
    }
}
