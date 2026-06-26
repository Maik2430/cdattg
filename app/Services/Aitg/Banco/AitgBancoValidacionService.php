<?php

namespace App\Services\Aitg\Banco;

use App\Models\Aitg\Banco\PostulacionArchivo;
use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\User;
use App\Services\Aitg\Evaluacion\AitgEvaluacionService;
use App\Services\Aitg\Postulacion\AitgPostulacionItemsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/** Validación documental: Banco de Talento (habilitación) vs postulación a convocatoria. */
class AitgBancoValidacionService
{
    public function __construct(
        private readonly AitgEvaluacionService $evaluacionService,
        private readonly AitgPostulacionItemsService $postulacionItemsService
    ) {}
    public function validarDocumento(PostulacionArchivo $archivoPostulacion, array $data, User $validador): void
    {
        DB::transaction(function () use ($archivoPostulacion, $data, $validador) {
            $this->aplicarValidacion($archivoPostulacion, $data, $validador);
            $postulacion = $archivoPostulacion->postulacion()->with(['archivos.tipoArchivo'])->first();
            $this->evaluarEstadoPostulacion($postulacion);
        });
    }

    public function validarDocumentosLote(PostulacionPlan $postulacion, array $validaciones, User $validador): void
    {
        DB::transaction(function () use ($postulacion, $validaciones, $validador) {
            $pendientes = $this->archivosPendientesValidacion($postulacion);

            foreach ($pendientes as $archivoPostulacion) {
                $data = $validaciones[$archivoPostulacion->id] ?? null;

                if (! $data || empty($data['resultado'])) {
                    throw new \InvalidArgumentException('Debe registrar una decisión para todos los documentos pendientes.');
                }

                if ($data['resultado'] === 'rechazado' && empty($data['motivo_rechazo_id'])) {
                    throw new \InvalidArgumentException('Debe indicar el motivo de rechazo en todos los documentos rechazados.');
                }

                $this->aplicarValidacion($archivoPostulacion, $data, $validador);
            }

            $this->evaluarEstadoPostulacion($postulacion->fresh(['archivos.tipoArchivo', 'user']));
        });
    }

    public function archivosPendientesValidacion(PostulacionPlan $postulacion): Collection
    {
        return $this->archivosFaseDocumental($postulacion)
            ->filter(fn (PostulacionArchivo $a) => in_array($a->estado, ['en_revision', 'pendiente', 'rechazado'], true));
    }

    public function archivosFaseDocumental(PostulacionPlan $postulacion): Collection
    {
        if ($postulacion->esConvocatoria() && $postulacion->faseDocumental() === 'post_seleccion') {
            $postulacion->loadMissing([
                'archivos.tipoArchivo',
                'archivos.archivoTalento',
                'archivos.validaciones.motivoRechazo',
            ]);

            return $this->archivosDeFase($postulacion, 'post_seleccion');
        }

        if ($postulacion->esConvocatoria()) {
            $postulacion->loadMissing([
                'archivos.tipoArchivo',
                'archivos.archivoTalento',
                'archivos.validaciones.motivoRechazo',
            ]);

            return $this->postulacionItemsService->archivosPostulacion($postulacion)
                ->merge($this->archivosDeFase($postulacion, 'inicial'))
                ->unique('id');
        }

        $postulacion->loadMissing([
            'archivos.tipoArchivo',
            'archivos.puntoAdicional',
            'archivos.archivoTalento',
            'archivos.validaciones.motivoRechazo',
        ]);

        return $this->archivosDeFase($postulacion, $postulacion->faseDocumental());
    }

    public function fueCorregidoTrasRechazo(PostulacionArchivo $archivoPostulacion): bool
    {
        $archivoPostulacion->loadMissing('validaciones');

        $ultima = $archivoPostulacion->validaciones->sortByDesc('fecha_validacion')->first();

        return $ultima?->resultado === 'rechazado'
            && in_array($archivoPostulacion->estado, ['en_revision', 'pendiente'], true);
    }

    public function motivoRechazoActual(PostulacionArchivo $archivoPostulacion): ?string
    {
        $archivoPostulacion->loadMissing('validaciones.motivoRechazo');

        $ultima = $archivoPostulacion->validaciones->sortByDesc('fecha_validacion')->first();

        if (! $ultima || $ultima->resultado !== 'rechazado') {
            return null;
        }

        $texto = trim(($ultima->motivoRechazo->nombre ?? '') . ' ' . ($ultima->descripcion ?? ''));

        return $texto !== '' ? $texto : null;
    }

    public function devolverPostulacion(PostulacionPlan $postulacion, User $validador, string $observacion): PostulacionPlan
    {
        $postulacion->update([
            'estado' => 'requiere_correccion',
            'observaciones_validador' => $observacion,
            'user_update_id' => $validador->id,
        ]);

        return $postulacion->fresh();
    }

    public function evaluarEstadoPostulacion(?PostulacionPlan $postulacion): void
    {
        if (! $postulacion || ! in_array($postulacion->estado, ['pendiente_revision'], true)) {
            return;
        }

        if (! $postulacion->perfil_plan_id && $postulacion->esConvocatoria()) {
            return;
        }

        $postulacion->loadMissing(['archivos.tipoArchivo', 'user', 'checklistItems']);
        $archivosFase = $this->archivosFaseDocumental($postulacion);

        if ($postulacion->checklistItems->isNotEmpty()) {
            $faltanObligatorios = $postulacion->checklistItems
                ->filter(fn ($i) => $i->es_obligatorio)
                ->contains(fn ($i) => ! $i->postulacion_archivo_id);

            if ($faltanObligatorios) {
                return;
            }
        }

        if ($archivosFase->isEmpty()) {
            return;
        }

        if ($archivosFase->contains(fn ($a) => $a->estado === 'rechazado')) {
            $postulacion->update([
                'estado' => 'requiere_correccion',
                'fase_actual' => $postulacion->fase_actual === 'post_seleccion' ? 'post_seleccion' : 'inicial',
                'observaciones_validador' => 'Uno o más documentos fueron rechazados. Corrija solo los indicados y vuelva a enviar.',
            ]);

            return;
        }

        if ($archivosFase->contains(fn ($a) => ! in_array($a->estado, ['aprobado'], true))) {
            return;
        }

        if ($postulacion->esConvocatoria() && $postulacion->faseDocumental() === 'post_seleccion') {
            $postulacion->update([
                'estado' => 'seleccionado',
                'fase_actual' => 'post_seleccion',
                'fecha_resolucion' => now(),
                'observaciones_validador' => 'Documentos de formalización validados. El proceso contractual puede continuar.',
            ]);

            return;
        }

        if ($postulacion->esConvocatoria()) {
            $this->acreditarBancoSiDocumentosBaseAprobados($postulacion);
        }

        if ($postulacion->esBancoTalento()) {
            $postulacion->update([
                'estado' => 'aprobado',
                'fase_actual' => 'inicial',
                'fecha_resolucion' => now(),
                'observaciones_validador' => 'Documentos acreditados. Ya puede postularse en convocatorias abiertas de esta competencia.',
            ]);

            return;
        }

        $postulacion->update([
            'estado' => 'preseleccionado',
            'fase_actual' => 'inicial',
            'fecha_resolucion' => now(),
            'observaciones_validador' => 'Documentos de postulación validados. Pasará al proceso de evaluación y selección.',
        ]);

        $postulacion = $postulacion->fresh();
        $this->postulacionItemsService->marcarPreseleccionada($postulacion);
        $this->evaluacionService->inicializarEvaluacion($postulacion);
    }

    private function aplicarValidacion(PostulacionArchivo $archivoPostulacion, array $data, User $validador): void
    {
        $archivoPostulacion->validaciones()->create([
            'postulacion_archivo_id' => $archivoPostulacion->id,
            'validador_user_id' => $validador->id,
            'resultado' => $data['resultado'],
            'motivo_rechazo_id' => $data['resultado'] === 'rechazado' ? ($data['motivo_rechazo_id'] ?? null) : null,
            'descripcion' => $data['descripcion'] ?? null,
            'fecha_validacion' => now(),
        ]);

        $estadoDoc = $data['resultado'] === 'aprobado' ? 'aprobado' : 'rechazado';
        $archivoPostulacion->update(['estado' => $estadoDoc]);
        $archivoPostulacion->archivoTalento?->update(['estado' => $estadoDoc]);
    }

    private function archivosDeFase(PostulacionPlan $postulacion, string $fase): Collection
    {
        return $postulacion->archivos->filter(function (PostulacionArchivo $vinculo) use ($fase) {
            if ($vinculo->perfil_plan_id) {
                return $fase === 'inicial';
            }

            if ($vinculo->punto_adicional_id) {
                return $fase === 'inicial';
            }

            $tipo = $vinculo->tipoArchivo;
            if (! $tipo) {
                return false;
            }

            return ($tipo->fase_carga ?? 'inicial') === $fase;
        });
    }

    private function acreditarBancoSiDocumentosBaseAprobados(PostulacionPlan $postulacionConvocatoria): void
    {
        $bancoPost = PostulacionPlan::query()
            ->where('user_id', $postulacionConvocatoria->user_id)
            ->where('competencia_id', $postulacionConvocatoria->competencia_id)
            ->whereNull('convocatoria_id')
            ->whereNotIn('estado', ['aprobado'])
            ->first();

        if (! $bancoPost) {
            return;
        }

        $bancoPost->loadMissing(['archivos.tipoArchivo']);
        $tipos = $this->archivosDeFase($bancoPost, 'inicial');

        if ($tipos->isEmpty()) {
            return;
        }

        if ($tipos->contains(fn ($a) => $a->estado !== 'aprobado')) {
            return;
        }

        $bancoPost->update([
            'estado' => 'aprobado',
            'fase_actual' => 'inicial',
            'fecha_resolucion' => now(),
            'observaciones_validador' => 'Documentos base acreditados durante la postulación a convocatoria.',
        ]);
    }
}
