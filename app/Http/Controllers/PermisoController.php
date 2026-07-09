<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Permiso\HandlesPermisoCrudReadActions;
use App\Http\Controllers\Concerns\Permiso\HandlesPermisoCrudWriteActions;
use App\Http\Controllers\Concerns\Permiso\HandlesPermisoDatatableActions;
use App\Http\Controllers\Concerns\Permiso\HandlesPermisoPermissionApiActions;
use App\Http\Controllers\Concerns\Permiso\HandlesPermisoRoleApiActions;
use App\Services\PermisoService;

class PermisoController extends Controller
{
    use HandlesPermisoCrudReadActions;
    use HandlesPermisoCrudWriteActions;
    use HandlesPermisoDatatableActions;
    use HandlesPermisoPermissionApiActions;
    use HandlesPermisoRoleApiActions;

    protected PermisoService $permisoService;

    public function __construct(PermisoService $permisoService)
    {
        $this->permisoService = $permisoService;
    }
}
