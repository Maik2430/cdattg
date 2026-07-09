<?php

namespace App\Services\Concerns\PersonaIngresoSalida;

trait HandlesPersonaIngresoSalidaTipoPersonaDisplayHelpers
{
    /**
     * Pluraliza y capitaliza un nombre de rol
     */
    protected function pluralizarYCapitalizar(string $nombre): string
    {
        $nombre = strtolower($nombre);

        // Reglas de pluralización básicas
        $plurales = [
            'instructor' => 'Instructores',
            'aprendiz' => 'Aprendices',
            'administrador' => 'Administradores',
            'super administrador' => 'Super Administradores',
            'visitante' => 'Visitantes',
            'aspirante' => 'Aspirantes',
        ];

        if (isset($plurales[$nombre])) {
            return $plurales[$nombre];
        }

        // Fallback: capitalizar y agregar 's' si no termina en 's'
        $capitalizado = ucwords($nombre);
        if (! str_ends_with(strtolower($capitalizado), 's')) {
            $capitalizado .= 's';
        }

        return $capitalizado;
    }

    /**
     * Obtiene el color CSS dinámicamente basado en el tipo de persona
     */
    protected function obtenerColorPorTipo(string $tipo): string
    {
        // Colores disponibles en AdminLTE
        $coloresDisponibles = [
            'bg-primary',
            'bg-secondary',
            'bg-success',
            'bg-danger',
            'bg-warning',
            'bg-info',
            'bg-dark',
        ];

        // Generar un índice determinístico basado en el tipo
        $hash = crc32($tipo);
        $indice = abs($hash) % count($coloresDisponibles);

        return $coloresDisponibles[$indice];
    }

    /**
     * Obtiene el icono FontAwesome dinámicamente basado en el tipo de persona
     */
    protected function obtenerIconoPorTipo(string $tipo): string
    {
        // Mapeo inteligente basado en palabras clave en el nombre del tipo
        $tipoLower = strtolower($tipo);

        // Detectar palabras clave y asignar iconos apropiados
        if (str_contains($tipoLower, 'instructor') || str_contains($tipoLower, 'profesor')) {
            return 'fa-chalkboard-teacher';
        }
        if (str_contains($tipoLower, 'aprendiz') || str_contains($tipoLower, 'estudiante')) {
            return 'fa-user-graduate';
        }
        if (str_contains($tipoLower, 'administrador') || str_contains($tipoLower, 'admin')) {
            if (str_contains($tipoLower, 'super')) {
                return 'fa-user-shield';
            }

            return 'fa-user-tie';
        }
        if (str_contains($tipoLower, 'visitante') || str_contains($tipoLower, 'invitado')) {
            return 'fa-user-friends';
        }
        if (str_contains($tipoLower, 'aspirante') || str_contains($tipoLower, 'candidato')) {
            return 'fa-user-plus';
        }
        if (str_contains($tipoLower, 'vigilante') || str_contains($tipoLower, 'seguridad')) {
            return 'fa-shield-alt';
        }
        if (str_contains($tipoLower, 'coordinador')) {
            return 'fa-user-cog';
        }

        // Fallback: icono genérico
        return 'fa-user';
    }
}
