<?php

namespace App\Http\Requests\Concerns\StoreFichaCaracterizacion;

trait HandlesStoreFichaCaracterizacionAuthorization
{
    public function authorize(): bool
    {
        $user = $this->user();
        $canCreate = $user->can('CREAR FICHA CARACTERIZACION') || $user->can('CREAR FICHA DE CARACTERIZACION');

        \Log::info('StoreFichaCaracterizacionRequest authorize', [
            'user_id' => $user->id,
            'user_roles' => $user->getRoleNames(),
            'can_create' => $canCreate,
            'has_crear_ficha' => $user->can('CREAR FICHA CARACTERIZACION'),
            'has_crear_ficha_de' => $user->can('CREAR FICHA DE CARACTERIZACION'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);

        return $canCreate;
    }
}
