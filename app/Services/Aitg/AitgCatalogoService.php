<?php

namespace App\Services\Aitg;

use App\Models\Aitg\PlanContratacion;
use App\Models\Parametro;
use App\Models\Competencia;
use App\Models\ProgramaFormacion;
use Illuminate\Support\Collection;

/** Catálogo académico y etiquetas del módulo AITG. */
class AitgCatalogoService
{
    public const TEMA_NIVELES_FORMACION = 6; // Tema parametrizado de niveles

    public function nivelesFormacion(): Collection
    {
        return Parametro::whereHas('temas', fn ($q) => $q->where('temas.id', self::TEMA_NIVELES_FORMACION))
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function competenciasActivas(): Collection
    {
        return Competencia::query()
            ->select(['id', 'codigo', 'nombre'])
            ->where('status', 1)
            ->orderBy('nombre')
            ->get();
    }

    public function programasActivos(): Collection
    {
        return ProgramaFormacion::query()
            ->select([
                'programas_formacion.id',
                'programas_formacion.codigo',
                'programas_formacion.nombre',
                'programas_formacion.nivel_formacion_id',
                'parametros_temas.parametro_id as nivel_parametro_id',
            ])
            ->join(
                'parametros_temas',
                'parametros_temas.id',
                '=',
                'programas_formacion.nivel_formacion_id'
            )
            ->where('programas_formacion.status', true)
            ->orderBy('programas_formacion.nombre')
            ->get();
    }

    public function etiquetaBloque(PlanContratacion $plan, int $consecutivo, ?int $total = null): string
    {
        $total ??= $plan->perfiles()->count() ?: 1;

        return match ($plan->tipo_registro_perfil) {
            'opcion' => $total === 1 ? 'Opción' : "Opción {$consecutivo}",
            'alternativa' => $total === 1 ? 'Alternativa' : "Alternativa {$consecutivo}",
            default => "Registro {$consecutivo}",
        };
    }

    public function etiquetaTipoRegistro(string $tipo): string
    {
        return match ($tipo) {
            'opcion' => 'Por opción',
            'alternativa' => 'Por alternativa',
            default => 'Por nivel de formación y programa',
        };
    }
}
