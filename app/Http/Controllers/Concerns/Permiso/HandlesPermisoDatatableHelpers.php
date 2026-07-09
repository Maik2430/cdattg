<?php

namespace App\Http\Controllers\Concerns\Permiso;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HandlesPermisoDatatableHelpers
{
    /**
     * @param  Builder<User>  $query
     */
    protected function applyPermisoDatatableSearch(Builder $query, ?string $searchValue): void
    {
        if (! $searchValue) {
            return;
        }

        $query->whereHas('persona', function ($innerQuery) use ($searchValue) {
            $innerQuery->where('primer_nombre', 'like', "%{$searchValue}%")
                ->orWhere('segundo_nombre', 'like', "%{$searchValue}%")
                ->orWhere('primer_apellido', 'like', "%{$searchValue}%")
                ->orWhere('segundo_apellido', 'like', "%{$searchValue}%")
                ->orWhere('numero_documento', 'like', "%{$searchValue}%")
                ->orWhere('email', 'like', "%{$searchValue}%");
        });
    }

    /**
     * @return array<int, string>
     */
    protected function getPermisoDatatableColumns(): array
    {
        return [
            0 => 'users.id',
            1 => 'personas.primer_nombre',
            2 => 'personas.numero_documento',
            3 => 'personas.email',
            4 => 'roles',
            5 => 'users.status',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildPermisoDatatableRow(User $user, int $index, int $start): array
    {
        $roles = $user->getRoleNames();
        $primaryRole = $roles->first() ?? 'Sin rol';
        $rolesHtml = '<span class="badge badge-primary">'.e($primaryRole).'</span>';
        if ($roles->count() > 1) {
            $rolesHtml .= '<small class="text-muted d-block">(+'.($roles->count() - 1).' más)</small>';
        }

        $statusBadge = $user->status === 1
            ? '<span class="badge badge-success">ACTIVO</span>'
            : '<span class="badge badge-danger">INACTIVO</span>';

        $acciones = '';
        if ($user->id != auth()->id()) {
            $acciones = '<div class="btn-group" role="group"><a class="btn btn-sm btn-light" href="'.route('permiso.show', $user->id).'" title="Ver Permisos"><i class="fas fa-eye text-warning"></i></a></div>';
        }

        return [
            'index' => $start + $index + 1,
            'nombre' => $user->persona->nombre_completo ?? 'N/A',
            'numero_documento' => $user->persona->numero_documento ?? 'N/A',
            'email' => $user->persona->email ?? 'N/A',
            'roles' => $rolesHtml,
            'estado' => $statusBadge,
            'acciones' => $acciones,
        ];
    }
}
