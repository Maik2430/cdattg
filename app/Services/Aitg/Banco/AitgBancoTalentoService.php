<?php

namespace App\Services\Aitg\Banco;

use App\Models\Aitg\Banco\ArchivoTalento;
use App\Models\Aitg\Banco\PostulacionArchivo;
use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\Banco\TipoArchivo;
use App\Models\Aitg\PlanContratacion;
use App\Models\Competencia;
use App\Models\Aitg\Postulacion\PostulacionChecklistItem;
use App\Models\Aitg\Postulacion\PostulacionPuntoItem;
use App\Models\User;
use App\Services\Aitg\Convocatoria\AitgConvocatoriaReglasService;
use App\Services\Aitg\Postulacion\AitgPostulacionItemsService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Gestión del banco de talento: postulaciones, bóveda de archivos y reutilización. */
class AitgBancoTalentoService
{
    private const STORAGE_FOLDER = 'aitg_banco_talento';

    public function __construct(
        private readonly AitgBancoRequisitosService $requisitosService,
        private readonly AitgConvocatoriaReglasService $convocatoriaReglasService,
        private readonly AitgPostulacionItemsService $postulacionItemsService
    ) {}

    public function buscarCompetencias(array $filtros): Collection
    {
        $query = Competencia::query()
            ->where('status', true)
            ->whereHas('aitgPlanes', fn ($q) => $q->whereIn('estado', ['activo', 'borrador']))
            ->with(['aitgPlanes' => fn ($q) => $q->whereIn('estado', ['activo', 'borrador'])->with('regional')->orderByDesc('created_at')])
            ->orderBy('nombre');

        if ($nombre = trim((string) ($filtros['competencia'] ?? ''))) {
            $query->where('nombre', 'like', "%{$nombre}%");
        }

        if ($regionalId = $filtros['regional_id'] ?? null) {
            $query->whereHas('aitgPlanes', fn ($q) => $q->where('regional_id', $regionalId));
        }

        if ($modalidad = $filtros['modalidad'] ?? null) {
            $query->whereHas('aitgPlanes', fn ($q) => $q->where('modalidad', $modalidad));
        }

        return $query->limit(20)->get();
    }

    /** @deprecated Use buscarCompetencias */
    public function buscarPlanes(array $filtros): Collection
    {
        return $this->buscarCompetencias($filtros);
    }

    public function listarPostulacionesUsuario(User $user): Collection
    {
        return $this->queryPostulacionesUsuario($user)->get();
    }

    public function listarPostulacionesBanco(User $user): Collection
    {
        return $this->queryPostulacionesUsuario($user)
            ->whereNull('convocatoria_id')
            ->get();
    }

    public function listarPostulacionesConvocatoria(User $user): Collection
    {
        return $this->queryPostulacionesUsuario($user)
            ->whereNotNull('convocatoria_id')
            ->get();
    }

    public function bancoHabilitadoParaCompetencia(User $user, int $competenciaId): ?PostulacionPlan
    {
        return PostulacionPlan::where('user_id', $user->id)
            ->where('competencia_id', $competenciaId)
            ->whereNull('convocatoria_id')
            ->where('estado', 'aprobado')
            ->first();
    }

    public function bancoHabilitadoParaPlan(User $user, int $planContratacionId): ?PostulacionPlan
    {
        $plan = PlanContratacion::find($planContratacionId);

        if (! $plan?->competencia_id) {
            return null;
        }

        return $this->bancoHabilitadoParaCompetencia($user, $plan->competencia_id);
    }

    private function queryPostulacionesUsuario(User $user)
    {
        return PostulacionPlan::with([
            'competencia',
            'plan.competencia',
            'plan.regional',
            'perfilPlan',
            'convocatoria',
            'archivos.tipoArchivo',
            'archivos.puntoAdicional',
            'archivos.validaciones.motivoRechazo',
        ])
            ->where('user_id', $user->id)
            ->orderByDesc('updated_at');
    }

    public function obtenerPostulacion(User $user, Competencia $competencia): PostulacionPlan
    {
        $existente = PostulacionPlan::where('user_id', $user->id)
            ->where('competencia_id', $competencia->id)
            ->whereNull('convocatoria_id')
            ->first();

        if ($existente) {
            return $existente->fresh(['archivos.tipoArchivo', 'competencia']);
        }

        return PostulacionPlan::create([
            'user_id' => $user->id,
            'competencia_id' => $competencia->id,
            'plan_contratacion_id' => null,
            'convocatoria_id' => null,
            'persona_id' => $user->persona?->id,
            'estado' => 'borrador',
            'fase_actual' => 'inicial',
            'user_create_id' => $user->id,
            'user_update_id' => $user->id,
        ])->fresh(['competencia']);
    }

    public function eliminarPostulacion(PostulacionPlan $postulacion, User $user): void
    {
        abort_unless($postulacion->user_id === $user->id, 403);
        abort_unless($postulacion->puedeEliminar(), 422, $postulacion->mensajeNoEliminable() ?? 'No puede eliminar esta postulación en el estado actual.');

        DB::transaction(function () use ($postulacion) {
            $postulacion->load(['archivos.archivoTalento', 'checklistItems', 'puntoItems']);

            foreach ($postulacion->archivos as $vinculo) {
                $this->eliminarVinculoArchivoCompleto($vinculo);
            }

            $postulacion->checklistItems()->delete();
            $postulacion->puntoItems()->delete();
            $postulacion->delete();
        });
    }

    private function eliminarVinculoArchivoCompleto(PostulacionArchivo $vinculo): void
    {
        $this->postulacionItemsService->desvincularArchivo($vinculo);

        $archivo = $vinculo->archivoTalento;
        $vinculo->validaciones()->delete();
        $vinculo->delete();

        if (! $archivo) {
            return;
        }

        if (! PostulacionArchivo::where('archivo_talento_id', $archivo->id)->exists()) {
            if (Storage::disk($archivo->storage_disk)->exists($archivo->storage_path)) {
                Storage::disk($archivo->storage_disk)->delete($archivo->storage_path);
            }
            $archivo->delete();
        }
    }

    public function obtenerPostulacionConvocatoria(User $user, \App\Models\Aitg\Convocatoria\Convocatoria $convocatoria): PostulacionPlan
    {
        $postulacionExistente = PostulacionPlan::where('user_id', $user->id)
            ->where('convocatoria_id', $convocatoria->id)
            ->first();

        if ($postulacionExistente) {
            $this->postulacionItemsService->instanciarDesdePlan($postulacionExistente->loadMissing('plan'));

            return $postulacionExistente->fresh([
                'checklistItems.postulacionArchivo',
                'puntoItems.postulacionArchivo',
            ]);
        }

        abort_unless($convocatoria->puedePostular(), 403, 'Esta convocatoria no acepta postulaciones.');

        $this->convocatoriaReglasService->validarPuedePostularConvocatoria($user, $convocatoria);

        $banco = $this->bancoHabilitadoParaCompetencia($user, $convocatoria->competencia_id);

        $postulacion = PostulacionPlan::create([
            'user_id' => $user->id,
            'convocatoria_id' => $convocatoria->id,
            'persona_id' => $user->persona?->id,
            'competencia_id' => $convocatoria->competencia_id,
            'plan_contratacion_id' => $convocatoria->plan_contratacion_id,
            'perfil_plan_id' => null,
            'estado' => 'borrador',
            'fase_actual' => 'inicial',
            'user_create_id' => $user->id,
            'user_update_id' => $user->id,
        ]);

        $postulacion = $this->postulacionItemsService->instanciarDesdePlan($postulacion);

        if ($banco) {
            $this->postulacionItemsService->precargarDesdeBanco($postulacion, $banco);
            $this->precargarDocumentosDesdeBanco($postulacion, $banco);
        }

        return $postulacion->fresh([
            'archivos.archivoTalento',
            'perfilPlan',
            'checklistItems.postulacionArchivo',
            'puntoItems.postulacionArchivo',
        ]);
    }

    /** Vincula los documentos aprobados del banco a una nueva postulación de convocatoria. */
    public function precargarDocumentosDesdeBanco(PostulacionPlan $postulacionConvocatoria, PostulacionPlan $banco): void
    {
        abort_unless($postulacionConvocatoria->esConvocatoria() && $banco->esBancoTalento(), 422);

        $banco->load('archivos');

        foreach ($banco->archivos as $vinculoBanco) {
            if ($vinculoBanco->estado !== 'aprobado') {
                continue;
            }

            $yaExiste = PostulacionArchivo::where('postulacion_id', $postulacionConvocatoria->id)
                ->where('tipo_archivo_id', $vinculoBanco->tipo_archivo_id)
                ->where('punto_adicional_id', $vinculoBanco->punto_adicional_id)
                ->exists();

            if ($yaExiste) {
                continue;
            }

            PostulacionArchivo::create([
                'postulacion_id' => $postulacionConvocatoria->id,
                'archivo_talento_id' => $vinculoBanco->archivo_talento_id,
                'tipo_archivo_id' => $vinculoBanco->tipo_archivo_id,
                'punto_adicional_id' => $vinculoBanco->punto_adicional_id,
                'estado' => 'pendiente',
            ]);
        }
    }

    public function listarPostulacionesDeConvocatoria(\App\Models\Aitg\Convocatoria\Convocatoria $convocatoria): Collection
    {
        return PostulacionPlan::with(['user.persona', 'perfilPlan'])
            ->where('convocatoria_id', $convocatoria->id)
            ->orderByDesc('updated_at')
            ->get();
    }

    public function seleccionarPerfil(PostulacionPlan $postulacion, int $perfilPlanId, User $user): PostulacionPlan
    {
        $perfil = $postulacion->plan->perfiles()->where('id', $perfilPlanId)->firstOrFail();

        $postulacion->update([
            'perfil_plan_id' => $perfil->id,
            'user_update_id' => $user->id,
        ]);

        $postulacion = $postulacion->fresh(['plan.perfiles', 'plan.checklist', 'plan.puntosAdicionales', 'perfilPlan']);
        $this->postulacionItemsService->instanciarDesdePlan($postulacion);

        return $postulacion->fresh([
            'plan.perfiles',
            'plan.checklist',
            'plan.puntosAdicionales',
            'perfilPlan',
            'checklistItems.postulacionArchivo',
            'puntoItems.postulacionArchivo',
        ]);
    }

    public function subirArchivo(
        PostulacionPlan $postulacion,
        UploadedFile $archivo,
        User $user,
        ?int $tipoArchivoId = null,
        ?int $puntoAdicionalId = null,
        ?int $checklistItemId = null,
        ?int $puntoItemId = null,
        ?int $perfilPlanId = null
    ): PostulacionArchivo {
        $postulacion = $this->resolverPostulacionParaCarga(
            $postulacion,
            $user,
            $tipoArchivoId,
            $checklistItemId,
            $puntoItemId
        );

        $plan = $postulacion->plan;
        $competenciaId = $postulacion->competencia_id ?? $plan?->competencia_id;
        abort_unless($competenciaId, 422, 'La postulación no tiene competencia asociada.');
        $tipo = $tipoArchivoId ? TipoArchivo::findOrFail($tipoArchivoId) : null;

        if ($puntoItemId && ! $puntoAdicionalId) {
            $puntoAdicionalId = PostulacionPuntoItem::where('postulacion_id', $postulacion->id)
                ->findOrFail($puntoItemId)
                ->punto_adicional_id;
        }

        $codigo = $tipo?->codigo ?? ($checklistItemId ? 'CHK' : ($perfilPlanId ? 'PERFIL' : 'PUNTO'));
        $nombreAlmacenado = $this->generarNombre($user, $codigo, $archivo);
        $disk = config('filesystems.aitg_banco_disk', 'public');
        $path = Storage::disk($disk)->putFileAs(self::STORAGE_FOLDER, $archivo, $nombreAlmacenado);

        if ($tipo && ! $tipo->permite_multiples) {
            $this->desvincularTipoAnterior($postulacion, $tipo->id);
        }

        $archivoTalento = ArchivoTalento::create([
            'user_id' => $user->id,
            'tipo_archivo_id' => $tipo?->id,
            'competencia_id' => $competenciaId,
            'plan_contratacion_id' => $postulacion->plan_contratacion_id,
            'perfil_plan_id' => $postulacion->perfil_plan_id,
            'punto_adicional_id' => $puntoAdicionalId,
            'storage_disk' => $disk,
            'storage_path' => $path,
            'nombre_original' => $archivo->getClientOriginalName(),
            'nombre_almacenado' => $nombreAlmacenado,
            'mime_type' => $archivo->getMimeType(),
            'tamano_bytes' => $archivo->getSize(),
            'estado' => 'pendiente',
            'user_create_id' => $user->id,
            'user_update_id' => $user->id,
        ]);

        $vinculo = $this->vincularArchivo($postulacion, $archivoTalento, $tipo?->id, $puntoAdicionalId, $perfilPlanId);

        if ($checklistItemId) {
            $item = PostulacionChecklistItem::where('postulacion_id', $postulacion->id)->findOrFail($checklistItemId);
            $this->postulacionItemsService->vincularArchivoChecklist($item, $vinculo);
        }

        if ($puntoItemId) {
            $item = PostulacionPuntoItem::where('postulacion_id', $postulacion->id)->findOrFail($puntoItemId);
            $this->postulacionItemsService->vincularArchivoPunto($item, $vinculo);
        }

        return $vinculo;
    }

    /** Carga múltiples documentos en un solo envío. */
    public function subirArchivosLote(PostulacionPlan $postulacion, array $archivos, User $user): int
    {
        $subidos = 0;

        foreach ($archivos as $key => $archivo) {
            if (! $archivo instanceof UploadedFile || ! $archivo->isValid()) {
                continue;
            }

            $tipoId = null;
            $puntoId = null;
            $checklistItemId = null;
            $puntoItemId = null;

            if (str_starts_with((string) $key, 'tipo_')) {
                $tipoId = (int) str_replace('tipo_', '', (string) $key);
            } elseif (str_starts_with((string) $key, 'punto_')) {
                $puntoId = (int) str_replace('punto_', '', (string) $key);
            } elseif (str_starts_with((string) $key, 'checklist_')) {
                $checklistItemId = (int) str_replace('checklist_', '', (string) $key);
            } elseif (str_starts_with((string) $key, 'puntoitem_')) {
                $puntoItemId = (int) str_replace('puntoitem_', '', (string) $key);
            }

            $this->subirArchivo(
                $postulacion,
                $archivo,
                $user,
                $tipoId ?: null,
                $puntoId ?: null,
                $checklistItemId ?: null,
                $puntoItemId ?: null
            );
            $subidos++;
        }

        return $subidos;
    }

    public function reutilizarArchivo(PostulacionPlan $postulacion, ArchivoTalento $archivo, User $user): PostulacionArchivo
    {
        abort_unless($archivo->user_id === $user->id, 403);

        if ($archivo->tipo_archivo_id) {
            $this->desvincularTipoAnterior($postulacion, $archivo->tipo_archivo_id);
        }

        return $this->vincularArchivo(
            $postulacion,
            $archivo,
            $archivo->tipo_archivo_id,
            $archivo->punto_adicional_id
        );
    }

    public function eliminarDocumentoPostulacion(PostulacionPlan $postulacion, PostulacionArchivo $vinculo, User $user): void
    {
        abort_unless($postulacion->puedeEditar(), 403);
        abort_unless($vinculo->postulacion_id === $postulacion->id, 404);

        $this->postulacionItemsService->desvincularArchivo($vinculo);

        $archivo = $vinculo->archivoTalento;
        $vinculo->delete();

        if ($archivo && ! PostulacionArchivo::where('archivo_talento_id', $archivo->id)->exists()) {
            if (Storage::disk($archivo->storage_disk)->exists($archivo->storage_path)) {
                Storage::disk($archivo->storage_disk)->delete($archivo->storage_path);
            }
            $archivo->delete();
        }
    }

    public function bovedaUsuario(User $user): Collection
    {
        return ArchivoTalento::with('tipoArchivo')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function seccionesDocumentales(PostulacionPlan $postulacion, User $user): array
    {
        if ($postulacion->requierePerfil() && $postulacion->faseDocumental() === 'inicial') {
            return [];
        }

        if ($postulacion->esConvocatoria() && $postulacion->faseDocumental() === 'post_seleccion') {
            return $this->seccionesTiposArchivo($postulacion, $user);
        }

        if ($postulacion->esBancoTalento()) {
            return $this->seccionesTiposArchivo($postulacion, $user);
        }

        $this->postulacionItemsService->instanciarDesdePlan($postulacion);

        $secciones = [];

        if ($this->requiereDocumentosBaseEnConvocatoria($postulacion, $user)) {
            $secciones = array_merge($secciones, $this->seccionesTiposArchivo($postulacion, $user));
        }

        return array_merge($secciones, $this->postulacionItemsService->construirSecciones($postulacion, $user));
    }

    /** Tipos de archivo del catálogo (HV, RUT, antecedentes, formalización…). */
    public function seccionesTiposArchivo(PostulacionPlan $postulacion, User $user): array
    {
        $secciones = $this->requisitosService->construirSecciones($postulacion, $user);

        return array_values(array_filter(
            $secciones,
            fn (array $seccion) => $seccion['key'] !== 'puntos_adicionales'
        ));
    }

    public function requiereDocumentosBaseEnConvocatoria(PostulacionPlan $postulacion, User $user): bool
    {
        if (! $postulacion->esConvocatoria()) {
            return false;
        }

        $competenciaId = $postulacion->competencia_id ?? $postulacion->plan?->competencia_id;

        return $competenciaId
            && ! $this->bancoHabilitadoParaCompetencia($user, $competenciaId);
    }

    /** @deprecated El banco ya no usa plan shadow; conservado por compatibilidad interna. */
    public function obtenerBancoPostulacionEnCurso(User $user, PlanContratacion $plan): PostulacionPlan
    {
        abort_unless($plan->competencia_id, 422, 'El plan no tiene competencia asociada.');

        return $this->obtenerPostulacion($user, $plan->competencia);
    }

    public function puedeEnviar(PostulacionPlan $postulacion, User $user): bool
    {
        if ($postulacion->requierePerfil() && $postulacion->faseDocumental() === 'inicial') {
            return false;
        }

        if ($postulacion->esConvocatoria() && $postulacion->faseDocumental() === 'post_seleccion') {
            return $this->puedeEnviarDocumentosBase($postulacion, $user);
        }

        if (! $this->postulacionItemsService->puedeEnviar($postulacion)) {
            return false;
        }

        if ($postulacion->esConvocatoria()) {
            if ($this->requiereDocumentosBaseEnConvocatoria($postulacion, $user)
                && ! $this->puedeEnviarDocumentosBase($postulacion, $user)) {
                return false;
            }

            return true;
        }

        return $this->puedeEnviarDocumentosBase($postulacion, $user);
    }

    public function puedeEnviarDocumentosBase(PostulacionPlan $postulacion, User $user): bool
    {
        $secciones = collect($this->seccionesTiposArchivo($postulacion, $user))->all();

        if ($postulacion->estado === 'requiere_correccion') {
            foreach ($secciones as $seccion) {
                foreach ($seccion['items'] as $item) {
                    if (! ($item['requiere_accion'] ?? false)) {
                        continue;
                    }

                    $vinculado = $item['vinculado'] ?? null;

                    if (! $vinculado || $vinculado->estado === 'rechazado') {
                        return false;
                    }
                }
            }

            return true;
        }

        foreach ($secciones as $seccion) {
            foreach ($seccion['items'] as $item) {
                if ($item['obligatorio'] && empty($item['vinculado'])) {
                    return false;
                }
            }
        }

        return true;
    }

    private function resolverPostulacionParaCarga(
        PostulacionPlan $postulacion,
        User $user,
        ?int $tipoArchivoId,
        ?int $checklistItemId,
        ?int $puntoItemId
    ): PostulacionPlan {
        return $postulacion;
    }

    public function enviarRevision(PostulacionPlan $postulacion, User $user): PostulacionPlan
    {
        if ($postulacion->requierePerfil()) {
            throw new \InvalidArgumentException('Debe seleccionar el perfil al que aplica antes de enviar.');
        }

        $postulacion->update([
            'estado' => 'pendiente_revision',
            'fecha_envio' => now(),
            'observaciones_validador' => null,
            'user_update_id' => $user->id,
        ]);

        if ($postulacion->esConvocatoria()) {
            $this->postulacionItemsService->marcarEnviada($postulacion);
            $this->marcarArchivosEnRevision($postulacion);

            return $postulacion->fresh(['checklistItems', 'puntoItems']);
        }

        $this->marcarArchivosEnRevision($postulacion);

        return $postulacion->fresh(['archivos.tipoArchivo']);
    }

    public function enviarFormalizacion(PostulacionPlan $postulacion, User $user): PostulacionPlan
    {
        abort_unless($postulacion->esConvocatoria(), 422);
        abort_unless(in_array($postulacion->estado, ['seleccionado', 'requiere_correccion'], true), 422, 'Solo el instructor seleccionado puede enviar la formalización.');
        abort_unless($postulacion->fase_actual === 'post_seleccion', 422);

        if (! $this->puedeEnviarDocumentosBase($postulacion, $user)) {
            throw new \InvalidArgumentException('Complete todos los documentos obligatorios de formalización.');
        }

        $postulacion->update([
            'estado' => 'pendiente_revision',
            'fecha_envio' => now(),
            'observaciones_validador' => null,
            'user_update_id' => $user->id,
        ]);

        $this->marcarArchivosEnRevision($postulacion);

        return $postulacion->fresh(['archivos.tipoArchivo']);
    }

    private function marcarArchivosEnRevision(PostulacionPlan $postulacion): void
    {
        $fase = $postulacion->faseDocumental();
        $marcados = collect();

        if ($postulacion->checklistItems()->exists() && $fase === 'inicial') {
            $this->postulacionItemsService->archivosPostulacion($postulacion)->each(function ($a) use ($marcados) {
                $a->update(['estado' => 'en_revision']);
                $marcados->push($a->id);
            });
        }

        $postulacion->loadMissing('archivos.tipoArchivo');

        foreach ($postulacion->archivos as $vinculo) {
            if ($marcados->contains($vinculo->id)) {
                continue;
            }

            if ($vinculo->perfil_plan_id && $fase === 'inicial') {
                $vinculo->update(['estado' => 'en_revision']);

                continue;
            }

            $tipo = $vinculo->tipoArchivo;
            if ($tipo && ($tipo->fase_carga ?? 'inicial') === $fase) {
                $vinculo->update(['estado' => 'en_revision']);
            }
        }
    }

    private function enviarRevisionDocumentosBase(PostulacionPlan $bancoPost, User $user): void
    {
        $bancoPost->update([
            'estado' => 'pendiente_revision',
            'fecha_envio' => now(),
            'observaciones_validador' => null,
            'user_update_id' => $user->id,
        ]);

        $this->marcarArchivosEnRevision($bancoPost);
    }

    private function vincularArchivo(
        PostulacionPlan $postulacion,
        ArchivoTalento $archivoTalento,
        ?int $tipoArchivoId,
        ?int $puntoAdicionalId,
        ?int $perfilPlanId = null
    ): PostulacionArchivo {
        if ($puntoAdicionalId) {
            PostulacionArchivo::where('postulacion_id', $postulacion->id)
                ->where('punto_adicional_id', $puntoAdicionalId)
                ->delete();
        }

        if ($perfilPlanId) {
            PostulacionArchivo::where('postulacion_id', $postulacion->id)
                ->where('perfil_plan_id', $perfilPlanId)
                ->delete();
        }

        return PostulacionArchivo::create([
            'postulacion_id' => $postulacion->id,
            'archivo_talento_id' => $archivoTalento->id,
            'tipo_archivo_id' => $tipoArchivoId,
            'punto_adicional_id' => $puntoAdicionalId,
            'perfil_plan_id' => $perfilPlanId,
            'estado' => 'pendiente',
        ]);
    }

    private function desvincularTipoAnterior(PostulacionPlan $postulacion, int $tipoArchivoId): void
    {
        PostulacionArchivo::where('postulacion_id', $postulacion->id)
            ->where('tipo_archivo_id', $tipoArchivoId)
            ->delete();
    }

    private function generarNombre(User $user, string $codigo, UploadedFile $archivo): string
    {
        return Str::upper($codigo) . "_{$user->id}_" . now()->format('YmdHis') . '.' . $archivo->getClientOriginalExtension();
    }
}
