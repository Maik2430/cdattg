<?php

namespace Database\Seeders\Aitg\Support;

use App\Models\Aitg\ChecklistPlan;
use App\Models\Aitg\PerfilPlan;
use App\Models\Aitg\PlanContratacion;
use App\Models\Aitg\PuntoAdicional;
use App\Models\Competencia;
use App\Models\ProgramaFormacion;
use App\Models\RedConocimiento;
use App\Models\Regional;
use Illuminate\Support\Facades\Auth;

/** Helper para crear programas académicos y planes AITG de demostración. */
class AitgFixtureHelper
{
    private ?int $redId = null;

    private ?int $regionalId = null;

    private ?int $userId = null;

    public function regionalId(): int
    {
        if ($this->regionalId) {
            return $this->regionalId;
        }

        $this->regionalId = Regional::where('status', 1)->value('id')
            ?? Regional::query()->value('id')
            ?? 1;

        return $this->regionalId;
    }

    public function userId(): int
    {
        if ($this->userId) {
            return $this->userId;
        }

        $this->userId = Auth::id() ?? 1;

        return $this->userId;
    }

    public function redConocimientoId(): int
    {
        if ($this->redId) {
            return $this->redId;
        }

        $red = RedConocimiento::query()->first();
        if (! $red) {
            $red = RedConocimiento::create([
                'nombre' => 'RED DEMO AITG',
                'regionals_id' => $this->regionalId(),
                'status' => 1,
                'user_create_id' => $this->userId(),
                'user_edit_id' => $this->userId(),
            ]);
        }

        $this->redId = $red->id;

        return $this->redId;
    }

    public function competencia(string $nombre, string $codigo = ''): Competencia
    {
        $existente = Competencia::where('nombre', strtoupper($nombre))->first();
        if ($existente) {
            return $existente;
        }

        return Competencia::create([
            'codigo' => preg_match('/^\d+$/', $codigo) ? $codigo : (string) random_int(100000, 999999),
            'nombre' => strtoupper($nombre),
            'descripcion' => 'Competencia demo AITG',
            'duracion' => 40,
            'fecha_inicio' => now()->startOfYear(),
            'fecha_fin' => now()->endOfYear(),
            'status' => 1,
            'user_create_id' => $this->userId(),
            'user_edit_id' => $this->userId(),
        ]);
    }

    public function programa(string $nombre, string $nivelNombre, string $codigo): ProgramaFormacion
    {
        $existente = ProgramaFormacion::where('nombre', strtoupper($nombre))->first();
        if ($existente) {
            return $existente;
        }

        $resolver = new AitgNivelResolver($this->userId());

        return ProgramaFormacion::create([
            'codigo' => preg_match('/^\d+$/', $codigo) ? $codigo : (string) random_int(900000, 999999),
            'nombre' => strtoupper($nombre),
            'red_conocimiento_id' => $this->redConocimientoId(),
            'nivel_formacion_id' => $resolver->parametroTemaId($nivelNombre),
            'horas_totales' => 1200,
            'horas_etapa_lectiva' => 800,
            'horas_etapa_productiva' => 400,
            'status' => true,
            'user_create_id' => $this->userId(),
            'user_edit_id' => $this->userId(),
        ]);
    }

    /** Arma fila de perfil con descripción de criterio y experiencia opcional. */
    public function perfil(
        string $descripcionCriterio,
        bool $incluyeExperiencia,
        int $experienciaRelacionada = 0,
        int $experienciaDocencia = 0,
        ?string $descripcionPrograma = null,
        bool $requiereDocumento = true,
        ?string $documentoNombre = null,
        ?string $documentoDescripcion = null,
        bool $documentoEsObligatorio = false,
    ): array {
        return [
            'descripcion_criterio' => $descripcionCriterio,
            'descripcion_criterio_programa' => $descripcionPrograma,
            'incluye_experiencia' => $incluyeExperiencia,
            'experiencia_relacionada_meses' => $incluyeExperiencia ? $experienciaRelacionada : 0,
            'experiencia_docencia_meses' => $incluyeExperiencia ? $experienciaDocencia : 0,
            'requiere_documento' => $requiereDocumento,
            'documento_nombre' => $documentoNombre ?? 'Certificación del perfil',
            'documento_descripcion' => $documentoDescripcion ?? 'Suba el PDF que acredite esta alternativa.',
            'documento_es_obligatorio' => $documentoEsObligatorio,
        ];
    }

    public function planDemoExiste(string $observaciones): bool
    {
        return PlanContratacion::where('observaciones', $observaciones)->exists();
    }

    /** Asigna competencia demo a planes creados antes de la migración. */
    public function repararPlanesSinCompetencia(): int
    {
        $competenciaId = Competencia::where('status', 1)->orderBy('nombre')->value('id');

        if (! $competenciaId) {
            return 0;
        }

        return PlanContratacion::whereNull('competencia_id')->update([
            'competencia_id' => $competenciaId,
            'user_update_id' => $this->userId(),
        ]);
    }

    /** Fila de checklist documental del plan. */
    public function checklistItem(
        string $nombre,
        string $descripcion,
        float $puntaje = 10,
        bool $obligatorio = true
    ): array {
        return [
            'nombre' => $nombre,
            'descripcion_criterio' => $descripcion,
            'puntaje' => $puntaje,
            'es_obligatorio' => $obligatorio,
        ];
    }

    /** Checklist estándar demo (Gastronomía / pruebas). */
    public function checklistDemoEstandar(): array
    {
        return [
            $this->checklistItem('Certificado SENA', 'Cargue el certificado expedido por el SENA.', 10),
            $this->checklistItem('Diploma Profesional', 'Cargue su diploma profesional en PDF.', 20),
            $this->checklistItem('Certificación Laboral', 'Cargue certificación de experiencia laboral.', 20),
            $this->checklistItem('Curso Integridad', 'Cargue el curso de integridad institucional.', 10),
        ];
    }

    public function sincronizarChecklistPlan(PlanContratacion $plan, array $items, bool $forzarCompleto = false): void
    {
        if ($plan->checklist()->exists() && ! $forzarCompleto) {
            return;
        }

        if ($forzarCompleto) {
            $plan->checklist()->delete();
        }

        foreach ($items as $index => $row) {
            ChecklistPlan::create([
                'plan_contratacion_id' => $plan->id,
                'consecutivo' => $index + 1,
                'nombre' => $row['nombre'],
                'descripcion_criterio' => $row['descripcion_criterio'],
                'puntaje' => $row['puntaje'] ?? 10,
                'es_obligatorio' => $row['es_obligatorio'] ?? true,
                'orden' => $index + 1,
            ]);
        }
    }

    public function crearPlan(array $planData, array $perfiles, array $puntos, array $checklist = []): PlanContratacion
    {
        if (! empty($planData['observaciones']) && $this->planDemoExiste($planData['observaciones'])) {
            return PlanContratacion::where('observaciones', $planData['observaciones'])->firstOrFail();
        }

        $plan = PlanContratacion::create([
            ...$planData,
            'regional_id' => $this->regionalId(),
            'user_create_id' => $this->userId(),
            'user_update_id' => $this->userId(),
        ]);

        foreach ($perfiles as $index => $perfil) {
            PerfilPlan::create([
                'plan_contratacion_id' => $plan->id,
                'consecutivo' => $index + 1,
                'descripcion_criterio' => $perfil['descripcion_criterio'],
                'descripcion_criterio_programa' => $perfil['descripcion_criterio_programa'] ?? null,
                'incluye_experiencia' => $perfil['incluye_experiencia'] ?? false,
                'experiencia_relacionada_meses' => $perfil['incluye_experiencia'] ? ($perfil['experiencia_relacionada_meses'] ?? 0) : 0,
                'experiencia_docencia_meses' => $perfil['incluye_experiencia'] ? ($perfil['experiencia_docencia_meses'] ?? 0) : 0,
                'requiere_documento' => $perfil['requiere_documento'] ?? true,
                'documento_nombre' => $perfil['documento_nombre'] ?? 'Certificación del perfil',
                'documento_descripcion' => $perfil['documento_descripcion'] ?? 'Suba el PDF que acredite esta alternativa.',
                'documento_es_obligatorio' => $perfil['documento_es_obligatorio'] ?? false,
            ]);
        }

        foreach ($puntos as $index => $punto) {
            PuntoAdicional::create([
                'plan_contratacion_id' => $plan->id,
                'consecutivo' => $index + 1,
                'descripcion' => $punto['descripcion'],
                'puntaje_adicional' => $punto['puntaje_adicional'],
                'orden' => $index + 1,
            ]);
        }

        $this->sincronizarChecklistPlan($plan, $checklist ?: $this->checklistDemoEstandar());

        return $plan->fresh(['checklist', 'puntosAdicionales', 'perfiles']);
    }
}
