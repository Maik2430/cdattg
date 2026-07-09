<?php

namespace App\Http\Controllers\Concerns\Persona;

trait HandlesPersonaPermissionConstants
{
    private const PERMISSION_VIEW_PROFILE = 'VER PERFIL';

    private const PERMISSION_VIEW_PERSON = 'VER PERSONA';

    private const PERMISSION_ASSIGN_PERMISSIONS = 'ASIGNAR PERMISOS';

    private const ERROR_USER_NOT_RESOLVED = 'No se pudo determinar el usuario autenticado.';
}
