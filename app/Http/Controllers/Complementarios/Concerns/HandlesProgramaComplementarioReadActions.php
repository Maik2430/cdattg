<?php

namespace App\Http\Controllers\Complementarios\Concerns;

use App\Models\Complementarios\ComplementarioOfertado;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

trait HandlesProgramaComplementarioReadActions
{
    public function index(): View
    {
        $programas = $this->complementarioService
            ->obtenerProgramas(['catalogo.modalidad.parametro', 'jornada', 'diasFormacion', 'ambiente']);

        $programas = $this->complementarioService->enriquecerProgramas($programas);

        return view('complementarios.programas.admin.index', array_merge(
            ['programas' => $programas],
            $this->complementarioService->obtenerDatosFormulario()
        ));
    }

    public function create(): View
    {
        return view('complementarios.programas.admin.create', $this->complementarioService->obtenerDatosFormulario());
    }

    public function programasPublicos(): View
    {
        $programas = $this->complementarioService
            ->obtenerProgramas(['catalogo.modalidad.parametro', 'jornada', 'diasFormacion'], estado: 1);

        $programas = $this->complementarioService->enriquecerProgramas($programas);

        return view('complementarios.programas.public.index', [
            'programas' => $programas,
            'tiposDocumento' => $this->complementarioService->getTiposDocumento(),
            'generos' => $this->complementarioService->getGeneros(),
        ]);
    }

    public function verProgramas(): View
    {
        $programas = $this->complementarioService
            ->obtenerProgramas(['catalogo.modalidad.parametro', 'jornada', 'diasFormacion']);

        $programas = $this->complementarioService->enriquecerProgramas($programas);

        return view('complementarios.ver_programas', ['programas' => $programas]);
    }

    public function verPrograma(ComplementarioOfertado $programa): View
    {
        $programa->load(['catalogo.modalidad.parametro', 'jornada', 'diasFormacion']);
        $programa = $this->complementarioService->enriquecerPrograma($programa);

        $programaData = [
            'id' => $programa->id,
            'nombre' => $programa->nombre,
            'justificacion' => $programa->justificacion,
            'requisitos_ingreso' => $programa->requisitos_ingreso,
            'duracion' => ($programa->duracion ?? 0).' horas',
            'icono' => $programa->icono,
            'modalidad' => $programa->modalidad_nombre ?? 'N/A',
            'jornada' => $programa->jornada_nombre ?? 'N/A',
            'dias' => $this->formatearDiasFormacion($programa),
            'dias_detalle' => $this->mapearDiasFormacionPublico($programa),
            'cupos' => $programa->cupos,
            'estado' => $programa->estado_label,
        ];

        return view('complementarios.programas.public.show', compact('programaData'));
    }

    public function show(ComplementarioOfertado $programa): View
    {
        $programa->load(['catalogo.modalidad.parametro', 'jornada', 'diasFormacion', 'ambiente.piso', 'competencias', 'raps', 'estado.parametro']);
        $programa = $this->complementarioService->enriquecerPrograma($programa);

        return view('complementarios.programas.admin.show', array_merge(
            ['programa' => $programa],
            $this->complementarioService->obtenerDatosFormulario()
        ));
    }

    public function edit(ComplementarioOfertado $programa): View
    {
        $programa->load(['catalogo.modalidad.parametro', 'jornada', 'diasFormacion', 'ambiente', 'competencias', 'raps', 'guiasAprendizaje']);

        $dias = $this->mapearDiasFormacion($programa);
        $datosFormulario = $this->complementarioService->obtenerDatosFormulario();

        return view('complementarios.programas.admin.edit', array_merge(
            [
                'programa' => $programa,
                'diasSeleccionados' => $dias,
                'competenciasSeleccionadas' => $programa->competencias->pluck('id')->toArray(),
                'rapsSeleccionados' => $programa->raps->pluck('id')->toArray(),
                'guiasSeleccionadas' => $programa->guiasAprendizaje->pluck('id')->toArray(),
            ],
            $datosFormulario
        ));
    }

    public function editApi(ComplementarioOfertado $programa): JsonResponse
    {
        $programa->load(['catalogo.modalidad.parametro', 'jornada', 'diasFormacion', 'ambiente']);
        $dias = $this->mapearDiasFormacion($programa);

        return response()->json([
            'id' => $programa->id,
            'codigo' => $programa->codigo,
            'nombre' => $programa->nombre,
            'justificacion' => $programa->justificacion,
            'requisitos_ingreso' => $programa->requisitos_ingreso,
            'duracion' => $programa->duracion,
            'cupos' => $programa->cupos,
            'estado' => $programa->estado,
            'modalidad_id' => $programa->modalidad_id,
            'jornada_id' => $programa->jornada_id,
            'ambiente_id' => $programa->ambiente_id,
            'ambiente_comentario' => $programa->ambiente_comentario,
            'dias' => $dias,
        ]);
    }
}
