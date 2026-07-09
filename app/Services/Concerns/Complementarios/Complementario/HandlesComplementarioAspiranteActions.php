<?php

namespace App\Services\Concerns\Complementarios\Complementario;

use App\Models\Complementarios\AspiranteComplementario;

trait HandlesComplementarioAspiranteActions
{
    /**
     * Verificar si un usuario ya está inscrito en un programa
     */
    public function verificarInscripcionExistente(int $personaId, int $programaId): bool
    {
        return $this->aspiranteRepository->existeInscripcion($personaId, $programaId);
    }

    /**
     * Crear aspirante complementario
     */
    public function crearAspirante($personaId, $programaId, $observaciones = null): AspiranteComplementario
    {
        return $this->aspiranteRepository->create([
            'persona_id' => $personaId,
            'complementario_id' => $programaId,
            'observaciones' => $observaciones,
            'estado' => 1,
        ]);
    }

    /**
     * Actualizar estado del aspirante
     */
    public function actualizarEstadoAspirante(int $aspiranteId, $estado): ?AspiranteComplementario
    {
        $aspirante = $this->aspiranteRepository->findById($aspiranteId);
        $this->aspiranteRepository->update($aspirante, ['estado' => $estado]);

        return $aspirante;
    }
}
