<?php

namespace App\Services\Aitg\Convocatoria;

use App\Models\Aitg\Convocatoria\Convocatoria;
use App\Models\Aitg\PlanContratacion;
use App\Models\User;
use Illuminate\Support\Collection;

class AitgConvocatoriaService
{
    public function __construct(
        private readonly AitgConvocatoriaEstadoService $estadoService
    ) {}

    public function generarCodigo(): string
    {
        $year = now()->format('Y');
        $ultimo = Convocatoria::where('codigo', 'like', "CONV-{$year}-%")
            ->orderByDesc('id')
            ->value('codigo');

        $secuencia = 1;
        if ($ultimo && preg_match('/CONV-\d{4}-(\d+)/', $ultimo, $m)) {
            $secuencia = (int) $m[1] + 1;
        }

        return sprintf('CONV-%s-%03d', $year, $secuencia);
    }

    public function listarAdmin(array $filtros = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $this->estadoService->sincronizarEstadosAutomaticos();

        $query = Convocatoria::with(['competencia', 'regional', 'centroFormacion'])
            ->withCount('postulaciones')
            ->orderByDesc('created_at');

        if ($estado = $filtros['estado'] ?? null) {
            $query->where('estado', $estado);
        }

        if ($busqueda = trim((string) ($filtros['busqueda'] ?? ''))) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('titulo', 'like', "%{$busqueda}%")
                    ->orWhere('codigo', 'like', "%{$busqueda}%")
                    ->orWhereHas('competencia', fn ($cq) => $cq->where('nombre', 'like', "%{$busqueda}%"));
            });
        }

        return $query->paginate(15)->appends($filtros);
    }

    public function listarParaUsuario(User $user, array $filtros = []): Collection
    {
        $this->estadoService->sincronizarEstadosAutomaticos();

        $query = Convocatoria::with(['competencia', 'regional', 'centroFormacion', 'postulacionSeleccionada.user.persona', 'postulacionSeleccionada.perfilPlan'])
            ->withCount('postulaciones')
            ->orderByDesc('fecha_inicio_publicacion');

        if ($user->can('VER CONVOCATORIA AITG')) {
            if ($estado = $filtros['estado'] ?? null) {
                $query->where('estado', $estado);
            }
        } else {
            $query->whereIn('estado', ['publicada', 'cerrada', 'finalizada']);
            if ($estado = $filtros['estado'] ?? null) {
                if (in_array($estado, ['publicada', 'cerrada', 'finalizada'], true)) {
                    $query->where('estado', $estado);
                }
            }
        }

        if ($competencia = trim((string) ($filtros['competencia'] ?? ''))) {
            $query->where(function ($q) use ($competencia) {
                $q->where('titulo', 'like', "%{$competencia}%")
                    ->orWhereHas('competencia', fn ($cq) => $cq->where('nombre', 'like', "%{$competencia}%"));
            });
        }

        if ($regionalId = $filtros['regional_id'] ?? null) {
            $query->where('regional_id', $regionalId);
        }

        return $query->limit(50)->get();
    }

    public function planesPorCompetencia(int $competenciaId): Collection
    {
        return PlanContratacion::with('competencia')
            ->where('competencia_id', $competenciaId)
            ->whereIn('estado', ['activo', 'borrador'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function crear(array $data, User $user): Convocatoria
    {
        $estado = $data['estado'] ?? 'borrador';
        if (! in_array($estado, Convocatoria::ESTADOS_MANUALES, true)) {
            $estado = 'borrador';
        }

        return Convocatoria::create([
            ...$data,
            'codigo' => $this->generarCodigo(),
            'estado' => $estado,
            'fecha_publicacion' => $estado === 'publicada' ? now() : null,
            'user_create_id' => $user->id,
            'user_update_id' => $user->id,
        ]);
    }

    public function actualizar(Convocatoria $convocatoria, array $data, User $user): Convocatoria
    {
        if (in_array($convocatoria->estado, ['cerrada', 'finalizada'], true)) {
            unset($data['estado']);
        } else {
            $nuevoEstado = $data['estado'] ?? $convocatoria->estado;
            if (! in_array($nuevoEstado, Convocatoria::ESTADOS_MANUALES, true)) {
                $data['estado'] = $convocatoria->estado;
            }
        }

        $estadoAnterior = $convocatoria->estado;
        $nuevoEstado = $data['estado'] ?? $convocatoria->estado;

        if ($estadoAnterior !== 'publicada' && $nuevoEstado === 'publicada') {
            $data['fecha_publicacion'] = now();
        }

        $data['user_update_id'] = $user->id;
        $convocatoria->update($data);

        $this->estadoService->sincronizarEstadosAutomaticos(collect([$convocatoria->fresh()]));

        return $convocatoria->fresh();
    }
}
