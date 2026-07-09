<?php

namespace App\Services\Concerns\Complementarios\Complementario;

use App\Models\Ambiente;
use App\Models\Competencia;
use App\Models\Complementarios\ComplementarioCatalogo;
use App\Models\GuiasAprendizaje;
use App\Models\ParametroTema;

trait HandlesComplementarioFormDataHelpers
{
    /**
     * Datos compartidos para vistas de gestión/admin.
     */
    public function obtenerDatosFormulario(): array
    {
        /** @var \Illuminate\Support\Collection<int, ComplementarioCatalogo> $catalogoProgramas */
        $catalogoProgramas = ComplementarioCatalogo::query()
            ->where('nivel_formacion', 'CURSO ESPECIAL')
            ->where('activo', true)
            ->orderBy('denominacion')
            ->get([
                'id',
                'prf_codigo',
                'denominacion',
                'duracion_horas',
                'requisitos_ingreso',
            ]);

        $modalidades = ParametroTema::query()
            ->where('tema_id', 5)
            ->with('parametro')
            ->get();

        $diasSemana = ParametroTema::query()
            ->where('tema_id', 4)
            ->with('parametro')
            ->orderBy('id')
            ->get();

        $jornadas = ParametroTema::whereHas('tema', function ($q): void {
            $q->where('name', 'LIKE', '%JORNADAS%');
        })->whereHas('parametro', function ($query): void {
            $query->where('status', true);
        })->where('status', true)
            ->with('parametro')
            ->get();

        $ambientes = Ambiente::query()
            ->with('piso')
            ->where('status', 1)
            ->orderBy('piso_id')
            ->orderBy('title')
            ->get();

        $competencias = Competencia::query()
            ->activos()
            ->ordenadoPorCodigo()
            ->get(['id', 'codigo', 'nombre']);

        $guias = GuiasAprendizaje::query()
            ->activas()
            ->porNombreAsc()
            ->get(['id', 'codigo', 'nombre']);

        return compact('catalogoProgramas', 'modalidades', 'diasSemana', 'jornadas', 'ambientes', 'competencias', 'guias');
    }

    /**
     * Obtener tipos de documento dinámicamente desde el tema-parametro
     */
    public function getTiposDocumento()
    {
        $temaTipoDocumento = $this->temaRepository->obtenerTiposDocumento();

        if (! $temaTipoDocumento) {
            return collect();
        }

        return $temaTipoDocumento->parametros()
            ->where('parametros_temas.status', 1)
            ->orderBy('parametros.name')
            ->get(['parametros.id', 'parametros.name']);
    }

    /**
     * Obtener géneros dinámicamente desde el tema-parametro
     */
    public function getGeneros()
    {
        $temaGenero = $this->temaRepository->obtenerGeneros();

        if (! $temaGenero) {
            return collect();
        }

        return $temaGenero->parametros()
            ->where('parametros_temas.status', 1)
            ->orderBy('parametros.name')
            ->get(['parametros.id', 'parametros.name']);
    }
}
