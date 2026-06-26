<?php

namespace App\Services\Aitg\Convocatoria;

use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\Convocatoria\Convocatoria;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/** Reglas de negocio para postulaciones a convocatorias. */
class AitgConvocatoriaReglasService
{
    /** Estados de postulación a convocatoria que bloquean otra inscripción en la misma regional. */
    private const ESTADOS_ACTIVOS_REGIONAL = [
        'borrador',
        'pendiente_revision',
        'requiere_correccion',
        'preseleccionado',
    ];

    public function validarPuedePostularConvocatoria(User $user, Convocatoria $convocatoria): void
    {
        if ($convocatoria->postulaciones()->where('user_id', $user->id)->exists()) {
            return;
        }

        $this->validarNuevaPostulacion($user, $convocatoria);
    }

    public function validarNuevaPostulacion(User $user, Convocatoria $convocatoria): void
    {
        if (! $convocatoria->regional_id) {
            return;
        }

        $conflicto = PostulacionPlan::query()
            ->where('user_id', $user->id)
            ->whereNotNull('convocatoria_id')
            ->where('convocatoria_id', '!=', $convocatoria->id)
            ->whereIn('estado', self::ESTADOS_ACTIVOS_REGIONAL)
            ->whereHas('convocatoria', fn ($q) => $q->where('regional_id', $convocatoria->regional_id))
            ->with('convocatoria.regional')
            ->first();

        if ($conflicto) {
            throw ValidationException::withMessages([
                'convocatoria' => 'Ya tiene una postulación activa en la regional '
                    . ($conflicto->convocatoria->regional->nombre ?? 'seleccionada')
                    . '. Solo puede postularse una vez por regional. Puede postular en otras regionales con documentos propios de cada convocatoria.',
            ]);
        }
    }

    public function puedePostularUsuario(User $user, Convocatoria $convocatoria): bool
    {
        if ($convocatoria->postulaciones()->where('user_id', $user->id)->exists()) {
            return $convocatoria->puedePostular()
                || $convocatoria->postulaciones()
                    ->where('user_id', $user->id)
                    ->whereIn('estado', ['borrador', 'requiere_correccion'])
                    ->exists();
        }

        if (! $convocatoria->puedePostular()) {
            return false;
        }

        try {
            $this->validarPuedePostularConvocatoria($user, $convocatoria);

            return true;
        } catch (ValidationException) {
            return false;
        }
    }

    /** Mensaje informativo cuando no tiene banco aprobado (no bloquea la postulación). */
    public function mensajeBancoRecomendado(User $user, Convocatoria $convocatoria): ?string
    {
        if ($this->bancoAprobadoParaPlan($user, $convocatoria->plan_contratacion_id)) {
            return null;
        }

        return 'Puede postular cargando sus documentos base y el checklist de esta convocatoria en un solo paso. '
            . 'Si ya tiene el Banco de Talento aprobado para esta competencia, solo deberá completar el checklist del plan.';
    }

    public function bancoAprobadoParaPlan(User $user, int $planContratacionId): bool
    {
        $plan = \App\Models\Aitg\PlanContratacion::find($planContratacionId);

        if (! $plan?->competencia_id) {
            return false;
        }

        return $this->bancoAprobadoParaCompetencia($user, $plan->competencia_id);
    }

    public function bancoAprobadoParaCompetencia(User $user, int $competenciaId): bool
    {
        return PostulacionPlan::where('user_id', $user->id)
            ->where('competencia_id', $competenciaId)
            ->whereNull('convocatoria_id')
            ->where('estado', 'aprobado')
            ->exists();
    }

    public function mensajeBloqueoPostulacion(User $user, Convocatoria $convocatoria): ?string
    {
        if ($convocatoria->postulaciones()->where('user_id', $user->id)->exists()) {
            return null;
        }

        try {
            $this->validarPuedePostularConvocatoria($user, $convocatoria);

            return null;
        } catch (ValidationException $e) {
            return collect($e->errors()['convocatoria'] ?? [])->first();
        }
    }

    /** @deprecated Use mensajeBloqueoPostulacion */
    public function mensajeBloqueoRegional(User $user, Convocatoria $convocatoria): ?string
    {
        return $this->mensajeBloqueoPostulacion($user, $convocatoria);
    }
}
