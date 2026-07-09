<?php

namespace App\Services\Concerns\PersonaIngresoSalida;

use App\Exceptions\PersonaException;
use App\Models\Persona;
use App\Models\PersonaIngresoSalida;
use Spatie\Permission\Models\Role;

trait HandlesPersonaIngresoSalidaTipoPersonaActions
{
    /**
     * Obtiene todos los tipos de persona disponibles dinámicamente desde la base de datos
     */
    public function obtenerTiposPersona(): array
    {
        return PersonaIngresoSalida::obtenerTiposPersonaDisponibles();
    }

    /**
     * Obtiene la configuración de visualización para cada tipo de persona
     * Genera la configuración dinámicamente basándose en los roles del sistema
     */
    public function obtenerConfiguracionTiposPersona(): array
    {
        $tiposPersona = $this->obtenerTiposPersona();
        $configuracion = [];

        // Obtener todos los roles del sistema
        $roles = Role::pluck('name')->mapWithKeys(function ($roleName) {
            return [strtoupper($roleName) => $roleName];
        })->toArray();

        // Mapeo dinámico de tipos de persona a roles del sistema
        // Busca el rol que coincida con el tipo (case-insensitive, sin espacios/guiones)
        $mapeoTipoARol = [];
        foreach ($tiposPersona as $tipo) {
            $tipoNormalizado = str_replace('_', ' ', strtoupper($tipo));
            foreach ($roles as $rolUpper => $rolName) {
                $rolNormalizado = str_replace(' ', '_', strtoupper($rolName));
                if ($tipoNormalizado === $rolNormalizado ||
                    str_contains($rolNormalizado, str_replace(' ', '_', $tipoNormalizado)) ||
                    str_contains($tipoNormalizado, str_replace('_', ' ', $rolNormalizado))) {
                    $mapeoTipoARol[$tipo] = $rolUpper;
                    break;
                }
            }
        }

        // Generar configuración para cada tipo de persona disponible
        foreach ($tiposPersona as $tipo) {
            $nombreRol = $mapeoTipoARol[$tipo] ?? null;
            $rolExiste = $nombreRol && isset($roles[$nombreRol]);

            // Obtener nombre del rol desde la base de datos si existe
            if ($rolExiste) {
                $nombre = $roles[$nombreRol];
                // Convertir a plural y capitalizar
                $nombre = $this->pluralizarYCapitalizar($nombre);
            } else {
                // Fallback: generar nombre desde el tipo
                $nombre = ucfirst(str_replace('_', ' ', $tipo));
                if (! str_ends_with(strtolower($nombre), 's')) {
                    $nombre .= 's';
                }
            }

            // Generar color dinámicamente basado en el tipo
            $color = $this->obtenerColorPorTipo($tipo);

            // Generar icono dinámicamente basado en el tipo
            $icono = $this->obtenerIconoPorTipo($tipo);

            $configuracion[$tipo] = [
                'nombre' => $nombre,
                'color' => $color,
                'icono' => $icono,
            ];

            // Estilo personalizado para super_administrador
            if ($tipo === 'super_administrador') {
                $configuracion[$tipo]['estilo_personalizado'] =
                    'background-color: #6f42c1 !important; color: white;';
            }
        }

        return $configuracion;
    }

    /**
     * Determina el tipo de persona basándose en sus relaciones
     */
    public function determinarTipoPersona(int $personaId): string
    {
        $persona = Persona::with(['instructor', 'aprendiz', 'user.roles'])->find($personaId);

        if (! $persona) {
            throw new PersonaException("Persona no encontrada con ID: {$personaId}");
        }

        $tipoPersona = 'visitante'; // Por defecto

        // Verificar si es instructor
        if ($persona->instructor) {
            $tipoPersona = 'instructor';
        } elseif ($persona->aprendiz) {
            // Verificar si es aprendiz
            $tipoPersona = 'aprendiz';
        } elseif ($persona->user) {
            // Verificar roles del usuario
            $roles = $persona->user->roles->pluck('name')->map(fn ($r) => strtoupper($r));

            if ($roles->contains('SUPER ADMINISTRADOR')) {
                $tipoPersona = 'super_administrador';
            } elseif ($roles->contains('ADMINISTRADOR')) {
                $tipoPersona = 'administrativo';
            } elseif ($roles->contains('VISITANTE')) {
                $tipoPersona = 'visitante';
            } elseif ($roles->contains('ASPIRANTE')) {
                $tipoPersona = 'aspirante';
            }
        }

        return $tipoPersona;
    }
}
