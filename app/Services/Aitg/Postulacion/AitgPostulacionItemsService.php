<?php

namespace App\Services\Aitg\Postulacion;

use App\Models\Aitg\Banco\ArchivoTalento;
use App\Models\Aitg\Banco\PostulacionArchivo;
use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\Postulacion\PostulacionChecklistItem;
use App\Models\Aitg\Postulacion\PostulacionPuntoItem;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/** Instancia y gestiona el checklist documental de cada postulación a convocatoria. */
class AitgPostulacionItemsService
{
    public function instanciarDesdePlan(PostulacionPlan $postulacion): PostulacionPlan
    {
        $postulacion->loadMissing(['plan.checklist', 'plan.puntosAdicionales', 'perfilPlan']);

        if ($postulacion->esBancoTalento() && ! $postulacion->perfil_plan_id) {
            return $postulacion;
        }

        if (! $postulacion->plan) {
            throw new \InvalidArgumentException('La postulación no tiene plan de contratación asociado.');
        }

        return DB::transaction(function () use ($postulacion) {
            if ($postulacion->checklistItems()->exists()) {
                return $postulacion->fresh(['checklistItems.postulacionArchivo.archivoTalento', 'puntoItems.postulacionArchivo.archivoTalento']);
            }

            foreach ($postulacion->plan->checklist as $index => $item) {
                PostulacionChecklistItem::create([
                    'postulacion_id' => $postulacion->id,
                    'checklist_plan_id' => $item->id,
                    'nombre' => $item->nombre ?: mb_substr($item->descripcion_criterio, 0, 255),
                    'descripcion_criterio' => $item->descripcion_criterio,
                    'puntaje' => $item->puntaje ?? 10,
                    'es_obligatorio' => $item->es_obligatorio ?? true,
                    'estado' => 'pendiente',
                    'orden' => $item->orden ?? ($index + 1),
                ]);
            }

            foreach ($postulacion->plan->puntosAdicionales as $index => $punto) {
                PostulacionPuntoItem::create([
                    'postulacion_id' => $postulacion->id,
                    'punto_adicional_id' => $punto->id,
                    'descripcion' => $punto->descripcion,
                    'puntaje_adicional' => $punto->puntaje_adicional ?? 0,
                    'es_opcional' => true,
                    'estado' => 'pendiente',
                    'orden' => $punto->orden ?? ($index + 1),
                ]);
            }

            return $postulacion->fresh(['checklistItems', 'puntoItems']);
        });
    }

    /** @return array<int, array<string, mixed>> */
    public function construirSecciones(PostulacionPlan $postulacion, User $user): array
    {
        $postulacion->loadMissing([
            'checklistItems.postulacionArchivo.archivoTalento',
            'checklistItems.postulacionArchivo.validaciones.motivoRechazo',
            'puntoItems.postulacionArchivo.archivoTalento',
            'puntoItems.postulacionArchivo.validaciones.motivoRechazo',
        ]);

        $modoSubsanacion = $postulacion->estado === 'requiere_correccion';
        $secciones = [];

        $checklistItems = [];
        foreach ($postulacion->checklistItems->sortBy('orden') as $item) {
            $vinculado = $item->postulacionArchivo;
            $checklistItems[] = $this->armarItemChecklist($item, $vinculado, $modoSubsanacion, $postulacion->checklistItems->count());
        }

        if ($checklistItems !== []) {
            $secciones[] = [
                'key' => 'checklist',
                'titulo' => 'Checklist documental',
                'items' => $this->filtrarSubsanacion($checklistItems, $modoSubsanacion),
            ];
        }

        $puntoItems = [];
        foreach ($postulacion->puntoItems->sortBy('orden') as $item) {
            $vinculado = $item->postulacionArchivo;
            $puntoItems[] = $this->armarItemPunto($item, $vinculado, $modoSubsanacion);
        }

        if ($puntoItems !== []) {
            $secciones[] = [
                'key' => 'puntos_adicionales',
                'titulo' => 'Puntos adicionales',
                'items' => $this->filtrarSubsanacion($puntoItems, $modoSubsanacion),
            ];
        }

        return $secciones;
    }

    /** Sección de documento PDF del perfil/alternativa seleccionada. */
    public function construirSeccionPerfil(PostulacionPlan $postulacion): ?array
    {
        $postulacion->loadMissing(['perfilPlan', 'archivos.archivoTalento', 'archivos.validaciones.motivoRechazo']);

        $perfil = $postulacion->perfilPlan;
        if (! $perfil || ! $perfil->requiere_documento) {
            return null;
        }

        $modoSubsanacion = $postulacion->estado === 'requiere_correccion';
        $vinculado = $postulacion->archivos
            ->first(fn (PostulacionArchivo $a) => $a->perfil_plan_id === $perfil->id);

        $nombre = $perfil->documento_nombre ?: 'Documento de acreditación del perfil';
        $descripcion = $perfil->documento_descripcion
            ?: 'Suba el PDF que acredite la alternativa seleccionada.';

        $item = [
            'tipo' => 'perfil',
            'perfil_plan_id' => $perfil->id,
            'checklist_item_id' => null,
            'punto_item_id' => null,
            'tipo_archivo_id' => null,
            'punto_adicional_id' => null,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'obligatorio' => (bool) $perfil->documento_es_obligatorio,
            'permite_multiples' => false,
            'vinculado' => $vinculado,
            'boveda_disponible' => collect(),
            'motivo_rechazo' => $this->motivoRechazo($vinculado),
            'requiere_accion' => $this->requiereAccion($vinculado, (bool) $perfil->documento_es_obligatorio, $modoSubsanacion, 'pendiente'),
        ];

        return [
            'key' => 'perfil',
            'titulo' => 'Documento del perfil seleccionado',
            'items' => $this->filtrarSubsanacion([$item], $modoSubsanacion),
        ];
    }

    public function vincularArchivoChecklist(
        PostulacionChecklistItem $item,
        PostulacionArchivo $vinculo
    ): PostulacionChecklistItem {
        abort_unless($item->postulacion_id === $vinculo->postulacion_id, 422);

        if ($item->postulacion_archivo_id && $item->postulacion_archivo_id !== $vinculo->id) {
            $this->eliminarVinculoArchivo($item->postulacionArchivo);
        }

        $item->update([
            'postulacion_archivo_id' => $vinculo->id,
            'estado' => 'cargado',
            'cumple' => null,
        ]);

        return $item->fresh(['postulacionArchivo.archivoTalento']);
    }

    public function vincularArchivoPunto(
        PostulacionPuntoItem $item,
        PostulacionArchivo $vinculo
    ): PostulacionPuntoItem {
        abort_unless($item->postulacion_id === $vinculo->postulacion_id, 422);

        if ($item->postulacion_archivo_id && $item->postulacion_archivo_id !== $vinculo->id) {
            $this->eliminarVinculoArchivo($item->postulacionArchivo);
        }

        $item->update([
            'postulacion_archivo_id' => $vinculo->id,
            'estado' => 'cargado',
            'cumple' => null,
        ]);

        return $item->fresh(['postulacionArchivo.archivoTalento']);
    }

    public function desvincularArchivo(PostulacionArchivo $vinculo): void
    {
        PostulacionChecklistItem::where('postulacion_archivo_id', $vinculo->id)->update([
            'postulacion_archivo_id' => null,
            'estado' => 'pendiente',
            'cumple' => null,
        ]);

        PostulacionPuntoItem::where('postulacion_archivo_id', $vinculo->id)->update([
            'postulacion_archivo_id' => null,
            'estado' => 'pendiente',
            'cumple' => null,
        ]);
    }

    public function puedeEnviar(PostulacionPlan $postulacion): bool
    {
        if ($postulacion->requierePerfil()) {
            return false;
        }

        $postulacion->loadMissing(['perfilPlan', 'archivos', 'checklistItems']);

        if ($postulacion->perfilPlan?->requiere_documento && $postulacion->perfilPlan->documento_es_obligatorio) {
            $tienePerfilDoc = $postulacion->archivos
                ->contains(fn (PostulacionArchivo $a) => $a->perfil_plan_id === $postulacion->perfil_plan_id);

            if (! $tienePerfilDoc) {
                return false;
            }
        }

        if ($postulacion->checklistItems->isEmpty()) {
            return true;
        }

        if ($postulacion->estado === 'requiere_correccion') {
            return $postulacion->checklistItems
                ->filter(fn (PostulacionChecklistItem $i) => in_array($i->estado, ['requiere_subsanacion', 'pendiente'], true)
                    || ($i->postulacionArchivo && $i->postulacionArchivo->estado === 'rechazado'))
                ->every(fn (PostulacionChecklistItem $i) => $i->tieneDocumento()
                    && $i->postulacionArchivo?->estado !== 'rechazado');
        }

        return $postulacion->checklistItems
            ->filter(fn (PostulacionChecklistItem $i) => $i->es_obligatorio)
            ->every(fn (PostulacionChecklistItem $i) => $i->tieneDocumento());
    }

    public function marcarEnviada(PostulacionPlan $postulacion): void
    {
        $postulacion->checklistItems()->update(['estado' => 'pendiente_evaluacion']);
        $postulacion->puntoItems()
            ->whereNotNull('postulacion_archivo_id')
            ->update(['estado' => 'pendiente_evaluacion']);
    }

    public function marcarPreseleccionada(PostulacionPlan $postulacion): void
    {
        $postulacion->checklistItems()
            ->whereIn('estado', ['cargado', 'pendiente_evaluacion'])
            ->update(['estado' => 'pendiente_evaluacion']);

        $postulacion->puntoItems()
            ->whereNotNull('postulacion_archivo_id')
            ->whereIn('estado', ['cargado', 'pendiente_evaluacion'])
            ->update(['estado' => 'pendiente_evaluacion']);
    }

    public function archivosPostulacion(PostulacionPlan $postulacion): Collection
    {
        $postulacion->loadMissing([
            'checklistItems.postulacionArchivo.archivoTalento',
            'checklistItems.postulacionArchivo.tipoArchivo',
            'checklistItems.postulacionArchivo.validaciones.motivoRechazo',
            'puntoItems.postulacionArchivo.archivoTalento',
            'puntoItems.postulacionArchivo.validaciones.motivoRechazo',
            'archivos.archivoTalento',
            'archivos.validaciones.motivoRechazo',
            'perfilPlan',
        ]);

        $archivos = collect();

        foreach ($postulacion->archivos as $vinculo) {
            if ($vinculo->perfil_plan_id || $vinculo->tipo_archivo_id) {
                $archivos->push($vinculo);
            }
        }

        foreach ($postulacion->checklistItems as $item) {
            if ($item->postulacionArchivo) {
                $archivos->push($item->postulacionArchivo);
            }
        }

        foreach ($postulacion->puntoItems as $item) {
            if ($item->postulacionArchivo) {
                $archivos->push($item->postulacionArchivo);
            }
        }

        return $archivos->unique('id')->values();
    }

    public function etiquetaArchivo(PostulacionArchivo $vinculo): string
    {
        $checklist = PostulacionChecklistItem::where('postulacion_archivo_id', $vinculo->id)->first();
        if ($checklist) {
            return $checklist->nombre;
        }

        $punto = PostulacionPuntoItem::where('postulacion_archivo_id', $vinculo->id)->first();
        if ($punto) {
            return $punto->descripcion;
        }

        return $vinculo->tipoArchivo?->nombre
            ?? $vinculo->puntoAdicional?->descripcion
            ?? $vinculo->perfilPlan?->documento_nombre
            ?? 'Documento';
    }

    /** Copia checklist, puntos y documento de perfil desde banco aprobado a convocatoria. */
    public function precargarDesdeBanco(PostulacionPlan $convocatoria, PostulacionPlan $banco): void
    {
        abort_unless($convocatoria->esConvocatoria() && $banco->esBancoTalento(), 422);

        $banco->loadMissing([
            'checklistItems.postulacionArchivo',
            'puntoItems.postulacionArchivo',
            'archivos',
        ]);
        $convocatoria->loadMissing(['checklistItems', 'puntoItems']);

        foreach ($convocatoria->checklistItems as $itemConv) {
            if ($itemConv->tieneDocumento()) {
                continue;
            }

            $itemBanco = $banco->checklistItems
                ->first(fn ($i) => $i->checklist_plan_id === $itemConv->checklist_plan_id
                    && $i->postulacion_archivo_id
                    && $i->postulacionArchivo?->estado === 'aprobado');

            if (! $itemBanco?->postulacionArchivo) {
                continue;
            }

            $nuevo = PostulacionArchivo::create([
                'postulacion_id' => $convocatoria->id,
                'archivo_talento_id' => $itemBanco->postulacionArchivo->archivo_talento_id,
                'tipo_archivo_id' => null,
                'punto_adicional_id' => null,
                'perfil_plan_id' => null,
                'estado' => 'pendiente',
            ]);

            $this->vincularArchivoChecklist($itemConv, $nuevo);
        }

        $this->precargarPuntosDesdeBanco($convocatoria, $banco);
    }

    public function precargarPuntosDesdeBanco(PostulacionPlan $convocatoria, PostulacionPlan $banco): void
    {
        $banco->loadMissing('archivos');

        foreach ($convocatoria->puntoItems as $puntoItem) {
            if ($puntoItem->tieneDocumento()) {
                continue;
            }

            $vinculoBanco = $banco->archivos
                ->first(fn (PostulacionArchivo $a) => $a->punto_adicional_id === $puntoItem->punto_adicional_id
                    && $a->estado === 'aprobado');

            if (! $vinculoBanco) {
                continue;
            }

            $nuevo = PostulacionArchivo::create([
                'postulacion_id' => $convocatoria->id,
                'archivo_talento_id' => $vinculoBanco->archivo_talento_id,
                'tipo_archivo_id' => null,
                'punto_adicional_id' => $puntoItem->punto_adicional_id,
                'estado' => 'pendiente',
            ]);

            $this->vincularArchivoPunto($puntoItem, $nuevo);
        }
    }

    /** Puntaje checklist = (cumplidos / N) × 100 con peso igual por ítem. */
    public function calcularPuntajeChecklistPorcentaje(PostulacionPlan $postulacion): float
    {
        $postulacion->loadMissing('checklistItems');
        $total = $postulacion->checklistItems->count();

        if ($total === 0) {
            return 0.0;
        }

        $cumplidos = $postulacion->checklistItems->filter(fn (PostulacionChecklistItem $i) => $i->cumple === true)->count();

        return round(($cumplidos / $total) * 100, 2);
    }

    public function calcularPuntajeAdicionales(PostulacionPlan $postulacion): float
    {
        $postulacion->loadMissing('puntoItems');

        return round(
            (float) $postulacion->puntoItems
                ->filter(fn (PostulacionPuntoItem $i) => $i->cumple === true)
                ->sum(fn (PostulacionPuntoItem $i) => (float) $i->puntaje_adicional),
            2
        );
    }

    public function pesoChecklistPorItem(int $totalItems): float
    {
        return $totalItems > 0 ? round(100 / $totalItems, 2) : 0.0;
    }

    private function armarItemChecklist(
        PostulacionChecklistItem $item,
        ?PostulacionArchivo $vinculado,
        bool $modoSubsanacion,
        int $totalChecklistItems = 1
    ): array {
        $pesoIgual = $this->pesoChecklistPorItem($totalChecklistItems);

        return [
            'tipo' => 'checklist',
            'checklist_item_id' => $item->id,
            'tipo_archivo_id' => null,
            'punto_adicional_id' => null,
            'nombre' => $item->nombre,
            'descripcion' => $item->descripcion_criterio,
            'puntaje' => $pesoIgual,
            'peso_porcentual' => $pesoIgual,
            'obligatorio' => $item->es_obligatorio,
            'permite_multiples' => false,
            'vinculado' => $vinculado,
            'boveda_disponible' => collect(),
            'motivo_rechazo' => $this->motivoRechazo($vinculado),
            'requiere_accion' => $this->requiereAccion($vinculado, $item->es_obligatorio, $modoSubsanacion, $item->estado),
        ];
    }

    private function armarItemPunto(
        PostulacionPuntoItem $item,
        ?PostulacionArchivo $vinculado,
        bool $modoSubsanacion
    ): array {
        return [
            'tipo' => 'punto',
            'punto_item_id' => $item->id,
            'tipo_archivo_id' => null,
            'punto_adicional_id' => $item->punto_adicional_id,
            'nombre' => $item->descripcion,
            'descripcion' => 'Puntaje adicional opcional: +'.number_format((float) $item->puntaje_adicional, 2).' pts si cumple en evaluación.',
            'puntaje' => $item->puntaje_adicional,
            'obligatorio' => false,
            'permite_multiples' => false,
            'vinculado' => $vinculado,
            'boveda_disponible' => collect(),
            'motivo_rechazo' => $this->motivoRechazo($vinculado),
            'requiere_accion' => $this->requiereAccion($vinculado, false, $modoSubsanacion, $item->estado),
        ];
    }

    /** @param array<int, array<string, mixed>> $items */
    private function filtrarSubsanacion(array $items, bool $modoSubsanacion): array
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

            return ($item['obligatorio'] ?? false) && ! $vinculado;
        }));
    }

    private function requiereAccion(
        ?PostulacionArchivo $vinculado,
        bool $obligatorio,
        bool $modoSubsanacion,
        string $estadoItem
    ): bool {
        if (! $modoSubsanacion) {
            return true;
        }

        if ($estadoItem === 'requiere_subsanacion') {
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

        $vinculado->loadMissing('validaciones.motivoRechazo');
        $ultima = $vinculado->validaciones->sortByDesc('fecha_validacion')->first();

        if (! $ultima || $ultima->resultado !== 'rechazado') {
            return null;
        }

        $texto = trim(($ultima->motivoRechazo->nombre ?? '').' '.($ultima->descripcion ?? ''));

        return $texto !== '' ? $texto : null;
    }

    private function eliminarVinculoArchivo(?PostulacionArchivo $vinculo): void
    {
        if (! $vinculo) {
            return;
        }

        $archivo = $vinculo->archivoTalento;
        $vinculo->delete();

        if ($archivo && ! PostulacionArchivo::where('archivo_talento_id', $archivo->id)->exists()) {
            $archivo->delete();
        }
    }
}
