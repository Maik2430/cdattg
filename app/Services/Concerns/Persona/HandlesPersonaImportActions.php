<?php

namespace App\Services\Concerns\Persona;

use App\Models\Persona;

trait HandlesPersonaImportActions
{
    /**
     * Crea una persona sin usuario (para importaciones masivas)
     */
    public function crearSinUsuario(array $datos, int $userId): Persona
    {
        $datos['user_create_id'] = $userId;
        $datos['user_edit_id'] = $userId;
        $datos['status'] = $datos['status'] ?? 1;

        return Persona::create($datos);
    }
}
