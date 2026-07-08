<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\GuiaAprendizaje\AnaliticasPolicy;
use App\Policies\Concerns\GuiaAprendizaje\ApiPolicy;
use App\Policies\Concerns\GuiaAprendizaje\ComentariosPolicy;
use App\Policies\Concerns\GuiaAprendizaje\CrudPolicy;
use App\Policies\Concerns\GuiaAprendizaje\DuplicacionPolicy;
use App\Policies\Concerns\GuiaAprendizaje\EstadoPolicy;
use App\Policies\Concerns\GuiaAprendizaje\EvidenciasPolicy;
use App\Policies\Concerns\GuiaAprendizaje\GuiaAprendizajePolicyHelpers;
use App\Policies\Concerns\GuiaAprendizaje\PlantillasPolicy;
use App\Policies\Concerns\GuiaAprendizaje\ReportePolicy;
use App\Policies\Concerns\GuiaAprendizaje\ResultadosPolicy;
use App\Policies\Concerns\GuiaAprendizaje\VersionesPolicy;

class GuiaAprendizajePolicy
{
    use AnaliticasPolicy;
    use ApiPolicy;
    use ComentariosPolicy;
    use CrudPolicy;
    use DuplicacionPolicy;
    use EstadoPolicy;
    use EvidenciasPolicy;
    use GuiaAprendizajePolicyHelpers;
    use PlantillasPolicy;
    use ReportePolicy;
    use ResultadosPolicy;
    use VersionesPolicy;

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
