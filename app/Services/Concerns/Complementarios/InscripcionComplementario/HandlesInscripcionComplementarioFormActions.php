<?php

namespace App\Services\Concerns\Complementarios\InscripcionComplementario;

use App\Models\Departamento;
use App\Models\Pais;
use Illuminate\Support\Facades\Auth;

trait HandlesInscripcionComplementarioFormActions
{
    public function prepararFormularioGeneral(): array
    {
        $categoriasConHijos = $this->obtenerCaracterizacionesAgrupadas();
        $paises = Pais::all();
        $departamentos = Departamento::all();
        $tiposDocumento = $this->complementarioService->getTiposDocumento();
        $generos = $this->complementarioService->getGeneros();

        $nivelEscolaridad = $this->buildTemaPayload($this->temaRepository->obtenerNivelEscolaridad());

        return compact('categoriasConHijos', 'paises', 'departamentos', 'tiposDocumento', 'generos', 'nivelEscolaridad');
    }

    public function prepararFormularioInscripcion(int $programaId): array
    {
        $programa = $this->programaRepository->findWithRelations($programaId, ['catalogo.modalidad.parametro', 'jornada']);

        if (! $programa) {
            abort(404, 'Programa no encontrado');
        }

        $documentos = $this->buildTemaPayload(
            $this->temaRepository->obtenerTiposDocumento(),
            $this->complementarioService->getTiposDocumento()
        );

        $generos = $this->buildTemaPayload(
            $this->temaRepository->obtenerGeneros(),
            $this->complementarioService->getGeneros()
        );

        $caracterizaciones = $this->buildTemaPayload(
            $this->temaRepository->obtenerCaracterizacionesComplementarias()
        );

        $vias = $this->buildTemaPayload($this->temaRepository->obtenerVias());
        $letras = $this->buildTemaPayload($this->temaRepository->obtenerLetras());
        $cardinales = $this->buildTemaPayload($this->temaRepository->obtenerCardinales());

        $nivelEscolaridad = $this->buildTemaPayload($this->temaRepository->obtenerNivelEscolaridad());

        $paises = Pais::all();
        $departamentos = Departamento::all();
        $municipios = collect();

        $categoriasConHijos = $this->obtenerCaracterizacionesAgrupadas($caracterizaciones);

        $personaAutenticada = Auth::check() ? Auth::user()->persona : null;

        return [
            'programa' => $programa,
            'categoriasConHijos' => $categoriasConHijos,
            'paises' => $paises,
            'departamentos' => $departamentos,
            'municipios' => $municipios,
            'documentos' => $documentos,
            'generos' => $generos,
            'caracterizaciones' => $caracterizaciones,
            'vias' => $vias,
            'letras' => $letras,
            'cardinales' => $cardinales,
            'nivelEscolaridad' => $nivelEscolaridad,
            'personaAutenticada' => $personaAutenticada,
        ];
    }
}
