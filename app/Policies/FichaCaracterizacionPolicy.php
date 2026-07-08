<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\FichaCaracterizacion\AprendicesPolicy;
use App\Policies\Concerns\FichaCaracterizacion\CrudPolicy;
use App\Policies\Concerns\FichaCaracterizacion\DiasFormacionPolicy;
use App\Policies\Concerns\FichaCaracterizacion\EstadisticasPolicy;
use App\Policies\Concerns\FichaCaracterizacion\EstadoPolicy;
use App\Policies\Concerns\FichaCaracterizacion\FichaCaracterizacionPolicyHelpers;
use App\Policies\Concerns\FichaCaracterizacion\ImportacionPolicy;
use App\Policies\Concerns\FichaCaracterizacion\InstructoresPolicy;
use App\Policies\Concerns\FichaCaracterizacion\ReportePolicy;
use App\Policies\Concerns\FichaCaracterizacion\SearchPolicy;

class FichaCaracterizacionPolicy
{
    use AprendicesPolicy;
    use CrudPolicy;
    use DiasFormacionPolicy;
    use EstadisticasPolicy;
    use EstadoPolicy;
    use FichaCaracterizacionPolicyHelpers;
    use ImportacionPolicy;
    use InstructoresPolicy;
    use ReportePolicy;
    use SearchPolicy;

    /**
     * Before hook - ejecutado antes de cualquier método de autorización.
     * Los super administradores tienen acceso total.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Super administradores tienen acceso total
        if ($user->hasRole('SUPER ADMINISTRADOR')) {
            return true;
        }

        return null; // Continuar con las verificaciones normales
    }
}
