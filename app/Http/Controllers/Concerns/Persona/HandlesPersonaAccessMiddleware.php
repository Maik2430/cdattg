<?php

namespace App\Http\Controllers\Concerns\Persona;

use App\Models\User;
use App\Repositories\TemaRepository;
use App\Services\PersonaService;
use App\Services\UbicacionService;

trait HandlesPersonaAccessMiddleware
{
    protected function registerPersonaAccessMiddleware(): void
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user instanceof User) {
                abort(403, self::ERROR_USER_NOT_RESOLVED);
            }
            $routeName = $request->route()->getName();

            if ($routeName === 'personas.mi-perfil' && $user->can(self::PERMISSION_VIEW_PROFILE)) {
                return $next($request);
            }

            if (
                $routeName === 'personas.show'
                && ($user->can(self::PERMISSION_VIEW_PROFILE) || $user->can(self::PERMISSION_VIEW_PERSON))
            ) {
                return $next($request);
            }

            if (
                $user->hasRole('ASPIRANTE')
                && ! $user->can(self::PERMISSION_VIEW_PERSON)
                && ! $user->can(self::PERMISSION_VIEW_PROFILE)
            ) {
                abort(403, 'No tienes permiso para acceder a este módulo.');
            }

            return $next($request);
        });
    }

    protected function registerPersonaPermissionMiddleware(): void
    {
        $this->middleware('can:VER PERSONA')->only(['index']);
        $this->middleware('can:CREAR PERSONA')->only(['create', 'store']);
        $this->middleware('can:EDITAR PERSONA')->only(['edit', 'update', 'createUser']);
        $this->middleware('can:ELIMINAR PERSONA')->only('destroy');
        $this->middleware('can:CAMBIAR ESTADO USUARIO')->only('cambiarEstadoUser');
        $this->middleware('can:ASIGNAR PERMISOS')->only('updateRole');
    }

    protected function initializePersonaController(
        PersonaService $personaService,
        UbicacionService $ubicacionService,
        TemaRepository $temaRepo
    ): void {
        $this->middleware('auth');
        $this->personaService = $personaService;
        $this->ubicacionService = $ubicacionService;
        $this->temaRepo = $temaRepo;

        $this->registerPersonaAccessMiddleware();
        $this->registerPersonaPermissionMiddleware();
    }
}
