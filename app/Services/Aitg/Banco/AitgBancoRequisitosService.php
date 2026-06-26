<?php

namespace App\Services\Aitg\Banco;

use App\Models\Aitg\Banco\ArchivoTalento;
use App\Models\Aitg\Banco\PostulacionArchivo;
use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\Banco\TipoArchivo;
use App\Models\User;

/** Arma las secciones de carga documental según perfil y plan. */
class AitgBancoRequisitosService
{
    public function construirSecciones(PostulacionPlan $postulacion, User $user): array
    {
        $postulacion->loadMissing([
            'plan.competencia',
            'plan.puntosAdicionales',
            'plan.checklist',
            'perfilPlan',
            'archivos.archivoTalento',
            'archivos.validaciones.motivoRechazo',
        ]);
        $perfil = $postulacion->perfilPlan;
        $fase = $postulacion->faseDocumental();
        $vinculados = $postulacion->archivos->keyBy(fn ($v) => $this->itemKey($v->tipo_archivo_id, $v->punto_adicional_id));
        $boveda = ArchivoTalento::where('user_id', $user->id)->orderByDesc('created_at')->get();
        $modoSubsanacion = $postulacion->estado === 'requiere_correccion';

        $secciones = [];

        foreach (TipoArchivo::CATEGORIAS as $categoriaKey => $categoriaLabel) {
            if ($categoriaKey === 'puntos_adicionales') {
                continue;
            }

            $tipos = TipoArchivo::activos()
                ->where('categoria', $categoriaKey)
                ->where('fase_carga', $fase)
                ->get();
            $items = [];

            foreach ($tipos as $tipo) {
                if (! $this->esVisible($tipo, $perfil)) {
                    continue;
                }

                $key = $this->itemKey($tipo->id, null);
                $items[] = $this->armarItem($tipo, $vinculados->get($key), $boveda, $tipo->id, null, $modoSubsanacion);
            }

            $items = $this->filtrarItemsSubsanacion($items, $modoSubsanacion);

            if ($items !== []) {
                $secciones[] = [
                    'key' => $categoriaKey,
                    'titulo' => $categoriaLabel,
                    'items' => $items,
                ];
            }
        }

        $puntosItems = [];
        if ($fase === 'inicial' && $postulacion->plan) {
            foreach ($postulacion->plan->puntosAdicionales as $punto) {
                $key = $this->itemKey(null, $punto->id);
                $vinculado = $vinculados->get($key);
                $puntosItems[] = [
                    'tipo' => null,
                    'punto' => $punto,
                    'obligatorio' => true,
                    'permite_multiples' => false,
                    'nombre' => $punto->descripcion,
                    'descripcion' => 'Cargue el soporte PDF que acredite este punto adicional del plan (puntaje: ' . number_format($punto->puntaje_adicional, 2) . ').',
                    'extensiones' => ['pdf'],
                    'vinculado' => $vinculado,
                    'boveda_disponible' => collect(),
                    'tipo_archivo_id' => null,
                    'punto_adicional_id' => $punto->id,
                    'motivo_rechazo' => $this->motivoRechazo($vinculado),
                    'requiere_accion' => $this->requiereAccionSubsanacion($vinculado, true, $modoSubsanacion),
                ];
            }
        }

        $puntosItems = $this->filtrarItemsSubsanacion($puntosItems, $modoSubsanacion);

        if ($puntosItems !== []) {
            $secciones[] = [
                'key' => 'puntos_adicionales',
                'titulo' => TipoArchivo::CATEGORIAS['puntos_adicionales'],
                'items' => $puntosItems,
            ];
        }

        return $secciones;
    }

    /** @param  array<int, array<string, mixed>>  $items */
    private function filtrarItemsSubsanacion(array $items, bool $modoSubsanacion): array
    {
        if (! $modoSubsanacion) {
            return $items;
        }

        return array_values(array_filter($items, function (array $item) {
            $vinculado = $item['vinculado'] ?? null;

            if ($vinculado && $vinculado->estado === 'aprobado') {
                return true;
            }

            if ($vinculado && $vinculado->estado === 'rechazado') {
                return true;
            }

            if (($item['obligatorio'] ?? false) && ! $vinculado) {
                return true;
            }

            return false;
        }));
    }

    private function requiereAccionSubsanacion(?PostulacionArchivo $vinculado, bool $obligatorio, bool $modoSubsanacion): bool
    {
        if (! $modoSubsanacion) {
            return true;
        }

        if (! $vinculado) {
            return $obligatorio;
        }

        return $vinculado->estado === 'rechazado';
    }

    private function motivoRechazo(?PostulacionArchivo $vinculado): ?string
    {
        if (! $vinculado) {
            return null;
        }

        $ultimaValidacion = $vinculado->validaciones->sortByDesc('fecha_validacion')->first();

        if (! $ultimaValidacion || $ultimaValidacion->resultado !== 'rechazado') {
            return null;
        }

        $texto = trim(($ultimaValidacion->motivoRechazo->nombre ?? '') . ' ' . ($ultimaValidacion->descripcion ?? ''));

        return $texto !== '' ? $texto : null;
    }

    private function esVisible(TipoArchivo $tipo, $perfil): bool
    {
        return match ($tipo->regla_visibilidad) {
            'requiere_perfil' => $perfil !== null,
            'requiere_exp_relacionada' => $perfil?->incluye_experiencia && ($perfil->experiencia_relacionada_meses ?? 0) > 0,
            'requiere_exp_docente' => $perfil?->incluye_experiencia && ($perfil->experiencia_docencia_meses ?? 0) > 0,
            default => true,
        };
    }

    private function armarItem(TipoArchivo $tipo, ?PostulacionArchivo $vinculado, $boveda, ?int $tipoId, ?int $puntoId, bool $modoSubsanacion): array
    {
        $disponibles = $boveda->filter(function (ArchivoTalento $archivo) use ($tipo, $vinculado) {
            if ($archivo->tipo_archivo_id !== $tipo->id) {
                return false;
            }
            if ($vinculado && $vinculado->archivo_talento_id === $archivo->id) {
                return false;
            }

            return true;
        })->values();

        return [
            'tipo' => $tipo,
            'punto' => null,
            'obligatorio' => $tipo->es_obligatorio,
            'permite_multiples' => $tipo->permite_multiples,
            'nombre' => $tipo->nombre,
            'descripcion' => $tipo->descripcion,
            'extensiones' => $tipo->extensiones_permitidas ?? ['pdf'],
            'vinculado' => $vinculado,
            'boveda_disponible' => $disponibles,
            'tipo_archivo_id' => $tipoId,
            'punto_adicional_id' => $puntoId,
            'motivo_rechazo' => $this->motivoRechazo($vinculado),
            'requiere_accion' => $this->requiereAccionSubsanacion($vinculado, $tipo->es_obligatorio, $modoSubsanacion),
        ];
    }

    private function itemKey(?int $tipoId, ?int $puntoId): string
    {
        return 't' . ($tipoId ?? '0') . '_p' . ($puntoId ?? '0');
    }
}
