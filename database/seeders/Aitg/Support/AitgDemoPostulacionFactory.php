<?php

namespace Database\Seeders\Aitg\Support;

use App\Models\Aitg\Banco\ArchivoTalento;
use App\Models\Aitg\Banco\PostulacionArchivo;
use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\Postulacion\PostulacionChecklistItem;
use App\Models\Aitg\Postulacion\PostulacionPuntoItem;
use App\Models\User;
use App\Services\Aitg\Banco\AitgBancoValidacionService;
use App\Services\Aitg\Evaluacion\AitgEvaluacionService;
use App\Services\Aitg\Postulacion\AitgPostulacionItemsService;
use Illuminate\Support\Facades\Storage;

/** Fabrica postulaciones demo con PDFs en checklist para probar validación → evaluación → selección. */
class AitgDemoPostulacionFactory
{
    private const STORAGE_FOLDER = 'aitg_banco_talento';

    public function __construct(
        private readonly AitgPostulacionItemsService $itemsService,
        private readonly AitgBancoValidacionService $validacionService,
        private readonly AitgEvaluacionService $evaluacionService,
    ) {}

    public function asegurarBancoAprobado(User $user, int $planId, int $adminId): PostulacionPlan
    {
        $banco = PostulacionPlan::firstOrCreate(
            [
                'user_id' => $user->id,
                'plan_contratacion_id' => $planId,
                'convocatoria_id' => null,
            ],
            [
                'persona_id' => $user->persona_id,
                'estado' => 'aprobado',
                'fase_actual' => 'inicial',
                'fecha_resolucion' => now(),
                'observaciones_validador' => 'Banco demo — acreditado para convocatoria de prueba.',
                'user_create_id' => $adminId,
                'user_update_id' => $adminId,
            ]
        );

        if ($banco->estado !== 'aprobado') {
            $banco->update([
                'estado' => 'aprobado',
                'fecha_resolucion' => now(),
                'observaciones_validador' => 'Banco demo — acreditado para convocatoria de prueba.',
            ]);
        }

        return $banco;
    }

    public function crearPostulacionConvocatoria(
        User $aspirante,
        int $convocatoriaId,
        int $planId,
        int $perfilPlanId,
        int $adminId
    ): PostulacionPlan {
        $postulacion = PostulacionPlan::updateOrCreate(
            [
                'user_id' => $aspirante->id,
                'convocatoria_id' => $convocatoriaId,
            ],
            [
                'plan_contratacion_id' => $planId,
                'persona_id' => $aspirante->persona_id,
                'perfil_plan_id' => $perfilPlanId,
                'estado' => 'borrador',
                'fase_actual' => 'inicial',
                'user_create_id' => $adminId,
                'user_update_id' => $adminId,
            ]
        );

        $postulacion = $this->itemsService->instanciarDesdePlan($postulacion->loadMissing('plan'));

        if ($postulacion->perfil_plan_id !== $perfilPlanId) {
            $postulacion->update(['perfil_plan_id' => $perfilPlanId]);
        }

        return $postulacion->fresh(['checklistItems', 'puntoItems']);
    }

    /** Carga PDF demo en cada ítem obligatorio del checklist y deja la postulación en revisión. */
    public function cargarChecklistYEnviar(PostulacionPlan $postulacion, User $aspirante): PostulacionPlan
    {
        $postulacion->loadMissing(['checklistItems', 'plan.competencia']);

        foreach ($postulacion->checklistItems as $item) {
            if ($item->postulacion_archivo_id) {
                continue;
            }

            $vinculo = $this->crearPdfChecklist($postulacion, $item, $aspirante);
            $vinculo->update(['estado' => 'en_revision']);
            $vinculo->archivoTalento?->update(['estado' => 'en_revision']);
        }

        $postulacion->update([
            'estado' => 'pendiente_revision',
            'fecha_envio' => now(),
        ]);

        $this->itemsService->marcarEnviada($postulacion);

        return $postulacion->fresh(['checklistItems.postulacionArchivo']);
    }

    public function validarDocumentos(PostulacionPlan $postulacion, User $validador): PostulacionPlan
    {
        $postulacion->update(['estado' => 'pendiente_revision']);

        $pendientes = $this->validacionService->archivosPendientesValidacion($postulacion->fresh());

        $validaciones = [];
        foreach ($pendientes as $archivo) {
            $validaciones[$archivo->id] = ['resultado' => 'aprobado'];
        }

        if ($validaciones !== []) {
            $this->validacionService->validarDocumentosLote($postulacion->fresh(), $validaciones, $validador);
        }

        return $postulacion->fresh(['checklistItems', 'evaluacion']);
    }

    public function evaluarYAprobar(PostulacionPlan $postulacion, User $evaluador): PostulacionPlan
    {
        if ($postulacion->estado !== 'preseleccionado') {
            $postulacion = $this->validarDocumentos($postulacion, $evaluador);
        }

        $postulacion->refresh();

        if ($postulacion->estado !== 'preseleccionado') {
            throw new \RuntimeException("Postulación #{$postulacion->id} no quedó preseleccionada tras validar.");
        }

        $evaluacion = $this->evaluacionService->inicializarEvaluacion($postulacion);

        $data = [
            'observaciones' => 'Evaluación demo — cumple criterios documentales.',
            'resultado' => 'aprobado',
            'checklist' => [],
            'puntos' => [],
        ];

        foreach ($postulacion->checklistItems as $item) {
            $data['checklist'][$item->id] = [
                'cumple' => '1',
                'observaciones' => 'Cumple (demo)',
                'solicita_actualizacion' => false,
            ];
        }

        foreach ($postulacion->puntoItems as $item) {
            if (! $item->postulacion_archivo_id) {
                continue;
            }
            $data['puntos'][$item->id] = [
                'cumple' => '1',
                'observaciones' => 'Cumple (demo)',
            ];
        }

        $this->evaluacionService->finalizarEvaluacion($evaluacion, $data, $evaluador);

        return $postulacion->fresh(['evaluacion', 'checklistItems']);
    }

    /** Evalúa con una fracción de ítems cumplidos (para demo de ranking). */
    public function evaluarConPorcentajeChecklist(
        PostulacionPlan $postulacion,
        User $evaluador,
        float $fraccionCumple = 1.0
    ): PostulacionPlan {
        if ($postulacion->estado !== 'preseleccionado') {
            $postulacion = $this->validarDocumentos($postulacion, $evaluador);
        }

        $postulacion->refresh();

        if ($postulacion->estado !== 'preseleccionado') {
            throw new \RuntimeException("Postulación #{$postulacion->id} no quedó preseleccionada tras validar.");
        }

        $evaluacion = $this->evaluacionService->inicializarEvaluacion($postulacion);
        $items = $postulacion->checklistItems->sortBy('orden')->values();
        $aCumplir = max(0, min($items->count(), (int) round($items->count() * $fraccionCumple)));

        $data = [
            'observaciones' => 'Evaluación demo — cumplimiento parcial para ranking.',
            'resultado' => 'aprobado',
            'checklist' => [],
            'puntos' => [],
        ];

        foreach ($items as $index => $item) {
            $data['checklist'][$item->id] = [
                'cumple' => $index < $aCumplir ? '1' : '0',
                'observaciones' => $index < $aCumplir ? 'Cumple (demo)' : 'No cumple (demo)',
                'solicita_actualizacion' => false,
            ];
        }

        foreach ($postulacion->puntoItems as $item) {
            if (! $item->postulacion_archivo_id) {
                continue;
            }
            $data['puntos'][$item->id] = [
                'cumple' => '1',
                'observaciones' => 'Cumple (demo)',
            ];
        }

        $this->evaluacionService->finalizarEvaluacion($evaluacion, $data, $evaluador);

        return $postulacion->fresh(['evaluacion', 'checklistItems']);
    }

    private function crearPdfChecklist(
        PostulacionPlan $postulacion,
        PostulacionChecklistItem $item,
        User $user
    ): PostulacionArchivo {
        $disk = config('filesystems.aitg_banco_disk', 'public');
        $nombreAlmacenado = sprintf(
            'demo_u%d_p%d_chk%d_%s.pdf',
            $user->id,
            $postulacion->id,
            $item->id,
            substr(md5($item->nombre), 0, 8)
        );
        $path = self::STORAGE_FOLDER.'/'.$nombreAlmacenado;

        if (! Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->put($path, "%PDF-1.4\n% Demo AITG — {$item->nombre}\n");
        }

        $archivoTalento = ArchivoTalento::create([
            'user_id' => $user->id,
            'tipo_archivo_id' => null,
            'competencia_id' => $postulacion->plan->competencia_id,
            'plan_contratacion_id' => $postulacion->plan_contratacion_id,
            'perfil_plan_id' => $postulacion->perfil_plan_id,
            'storage_disk' => $disk,
            'storage_path' => $path,
            'nombre_original' => str_replace(' ', '_', $item->nombre).'_demo.pdf',
            'nombre_almacenado' => $nombreAlmacenado,
            'mime_type' => 'application/pdf',
            'tamano_bytes' => Storage::disk($disk)->size($path),
            'estado' => 'pendiente',
            'user_create_id' => $user->id,
            'user_update_id' => $user->id,
        ]);

        $vinculo = PostulacionArchivo::create([
            'postulacion_id' => $postulacion->id,
            'archivo_talento_id' => $archivoTalento->id,
            'tipo_archivo_id' => null,
            'estado' => 'pendiente',
        ]);

        $this->itemsService->vincularArchivoChecklist($item, $vinculo);

        return $vinculo;
    }
}
