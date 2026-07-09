<?php

namespace App\Http\Requests\Concerns\Instructor;

use App\Models\Instructor;
use Carbon\Carbon;

trait HandlesInstructorBusinessRulesHelpers
{
    protected function validateBusinessRules($validator): void
    {
        $instructorId = $this->route('instructor');
        $regionalId = $this->input('regional_id');
        $especialidades = $this->input('especialidades', []);

        if ($this->hasFichasSuperpuestas($instructorId, $validator)) {
            $validator->errors()->add('fichas', 'El instructor tiene fichas con fechas superpuestas.');
        }

        if ($this->exceedsMaxFichasActivas($instructorId, $validator)) {
            $validator->errors()->add('fichas', 'El instructor excede el límite máximo de fichas activas (5 fichas).');
        }

        if ($this->isCreating() && empty($especialidades['principal'])) {
            $validator->errors()->add('especialidades.principal', 'El instructor debe tener al menos una especialidad principal.');
        }

        if (! $this->validateEspecialidadesPorRegional($especialidades, $regionalId, $validator)) {
            $validator->errors()->add('especialidades', 'Las especialidades deben pertenecer a la regional del instructor.');
        }

        if ($this->input('anos_experiencia') && $this->input('anos_experiencia') < 1) {
            $validator->errors()->add('anos_experiencia', 'El instructor debe tener al menos 1 año de experiencia.');
        }

        if ($this->hasConflictsWithNewFichas($instructorId, $validator)) {
            $validator->errors()->add('disponibilidad', 'El instructor no está disponible para nuevas asignaciones en el período solicitado.');
        }
    }

    protected function hasFichasSuperpuestas($instructorId, $validator): bool
    {
        if (! $instructorId) {
            return false;
        }

        $instructor = Instructor::find($instructorId);
        if (! $instructor) {
            return false;
        }

        $fichas = $instructor->instructorFichas()
            ->with('ficha')
            ->whereHas('ficha', function ($q) {
                $q->where('status', true)
                    ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->get();

        foreach ($fichas as $instructorFicha) {
            $ficha = $instructorFicha->ficha;
            $fechaInicio = Carbon::parse($ficha->fecha_inicio);
            $fechaFin = Carbon::parse($ficha->fecha_fin);

            $hasOverlap = $instructor->instructorFichas()
                ->where('id', '!=', $instructorFicha->id)
                ->whereHas('ficha', function ($q) use ($fechaInicio, $fechaFin) {
                    $q->where('status', true)
                        ->where(function ($query) use ($fechaInicio, $fechaFin) {
                            $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                                ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                                ->orWhere(function ($subQuery) use ($fechaInicio, $fechaFin) {
                                    $subQuery->where('fecha_inicio', '<=', $fechaInicio)
                                        ->where('fecha_fin', '>=', $fechaFin);
                                });
                        });
                })
                ->exists();

            if ($hasOverlap) {
                return true;
            }
        }

        return false;
    }

    protected function exceedsMaxFichasActivas($instructorId, $validator): bool
    {
        if (! $instructorId) {
            return false;
        }

        $instructor = Instructor::find($instructorId);
        if (! $instructor) {
            return false;
        }

        $fichasActivas = $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) {
                $q->where('status', true)
                    ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->count();

        return $fichasActivas > 5;
    }
}
