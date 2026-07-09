<?php

namespace App\Http\Controllers\Concerns\Persona;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

trait HandlesPersonaShowHelpers
{
    /**
     * @return array<int, string>
     */
    protected function getPersonaShowRelationsForFullAccess(): array
    {
        return [
            'tipoDocumento',
            'tipoGenero',
            'pais',
            'departamento',
            'municipio',
            'caracterizacionesComplementarias',
            'caracterizacion',
            'user.roles',
            'userCreatedBy.persona',
            'userUpdatedBy.persona',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function getPersonaShowRelationsForProfile(): array
    {
        return [
            'tipoDocumento',
            'tipoGenero',
            'pais',
            'departamento',
            'municipio',
            'caracterizacionesComplementarias',
            'caracterizacion',
            'user.roles',
        ];
    }

    protected function getPersonaRolesAsignados(Persona $persona): Collection
    {
        return $persona->user?->roles
            ? $persona->user->roles->pluck('name')->unique()->values()
            : collect();
    }

    protected function renderPersonaShowFullAccess(Persona $persona, User $user)
    {
        $persona->loadMissing($this->getPersonaShowRelationsForFullAccess());
        $rolesAsignados = $this->getPersonaRolesAsignados($persona);
        $rolesDisponibles = $user->can(self::PERMISSION_ASSIGN_PERMISSIONS)
            ? Role::orderBy('name')->get()
            : collect();

        return view('personas.show', [
            'persona' => $persona,
            'soloPerfil' => false,
            'rolesDisponibles' => $rolesDisponibles,
            'rolesAsignados' => $rolesAsignados,
        ]);
    }

    protected function renderPersonaShowProfileOnly(Persona $persona)
    {
        $persona->loadMissing($this->getPersonaShowRelationsForProfile());
        $rolesAsignados = $this->getPersonaRolesAsignados($persona);

        return view('personas.show', [
            'persona' => $persona,
            'soloPerfil' => true,
            'rolesDisponibles' => collect(),
            'rolesAsignados' => $rolesAsignados,
        ]);
    }
}
