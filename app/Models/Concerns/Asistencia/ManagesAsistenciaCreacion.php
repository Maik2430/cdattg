<?php

namespace App\Models\Concerns\Asistencia;

trait ManagesAsistenciaCreacion
{
    /**
     * Validar que no exista una asistencia activa para la misma ficha
     */
    public static function validarAsistenciaUnica(int $fichaId): ?self
    {
        return self::deFicha($fichaId)
            ->activa()
            ->first();
    }

    /**
     * Crear nueva asistencia con validaciones
     */
    public static function crearNueva(array $datos): self
    {
        $asistenciaActiva = self::validarAsistenciaUnica($datos['instructor_ficha_id']);

        if ($asistenciaActiva) {
            throw new \Exception('Ya existe una asistencia activa para esta ficha. Finalice la asistencia actual antes de crear una nueva.');
        }

        return self::create(array_merge($datos, [
            'is_finished' => false,
            'user_create_id' => auth()->id(),
            'user_edit_id' => auth()->id(),
        ]));
    }
}
