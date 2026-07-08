<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\Instructor\BackupPolicy;
use App\Policies\Concerns\Instructor\CompetenciaPolicy;
use App\Policies\Concerns\Instructor\CrudPolicy;
use App\Policies\Concerns\Instructor\DocumentoPolicy;
use App\Policies\Concerns\Instructor\EspecialidadPolicy;
use App\Policies\Concerns\Instructor\EstadoPolicy;
use App\Policies\Concerns\Instructor\EvaluacionPolicy;
use App\Policies\Concerns\Instructor\FichaPolicy;
use App\Policies\Concerns\Instructor\HorarioPolicy;
use App\Policies\Concerns\Instructor\ImportPolicy;
use App\Policies\Concerns\Instructor\InstructorPolicyHelpers;
use App\Policies\Concerns\Instructor\NotificacionPolicy;
use App\Policies\Concerns\Instructor\PerfilPolicy;
use App\Policies\Concerns\Instructor\ReportePolicy;
use App\Policies\Concerns\Instructor\SesionPolicy;

class InstructorPolicy
{
    use BackupPolicy;
    use CompetenciaPolicy;
    use CrudPolicy;
    use DocumentoPolicy;
    use EspecialidadPolicy;
    use EstadoPolicy;
    use EvaluacionPolicy;
    use FichaPolicy;
    use HorarioPolicy;
    use ImportPolicy;
    use InstructorPolicyHelpers;
    use NotificacionPolicy;
    use PerfilPolicy;
    use ReportePolicy;
    use SesionPolicy;

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
