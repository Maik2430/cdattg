<?php

namespace App\Http\Controllers\Complementarios\Concerns;

use App\Http\Requests\Complementarios\BuscarPersonaRequest;
use App\Models\Departamento;
use App\Models\Pais;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

trait HandlesAspiranteComplementarioFormActions
{
    public function buscarPersona(BuscarPersonaRequest $request): JsonResponse
    {
        $persona = $this->personaService->buscarPorDocumento(trim($request->validated()['numero_documento']));

        if (! $persona) {
            return response()->json([
                'success' => false,
                'found' => false,
                'message' => 'Persona no encontrada.',
            ]);
        }

        $persona->loadMissing(['tipoDocumento', 'tipoGenero', 'pais', 'departamento', 'municipio', 'caracterizacionesComplementarias']);

        return response()->json([
            'success' => true,
            'found' => true,
            'persona' => [
                'id' => $persona->id,
                'tipo_documento_id' => $persona->tipo_documento,
                'tipo_documento' => $persona->tipoDocumento ? $persona->tipoDocumento->name : null,
                'numero_documento' => $persona->numero_documento,
                'primer_nombre' => $persona->primer_nombre,
                'segundo_nombre' => $persona->segundo_nombre,
                'primer_apellido' => $persona->primer_apellido,
                'segundo_apellido' => $persona->segundo_apellido,
                'fecha_nacimiento' => $persona->fecha_nacimiento,
                'genero_id' => $persona->genero,
                'genero' => $persona->tipoGenero ? $persona->tipoGenero->name : null,
                'telefono' => $persona->telefono,
                'celular' => $persona->celular,
                'email' => $persona->email,
                'pais_id' => $persona->pais_id,
                'pais' => $persona->pais ? $persona->pais->pais : null,
                'departamento_id' => $persona->departamento_id,
                'departamento' => $persona->departamento ? $persona->departamento->departamento : null,
                'municipio_id' => $persona->municipio_id,
                'municipio' => $persona->municipio ? $persona->municipio->municipio : null,
                'direccion' => $persona->direccion,
                'caracterizaciones' => $persona->caracterizacionesComplementarias->pluck('id')->toArray(),
            ],
        ]);
    }

    public function create(int $programa): View
    {
        $data = $this->aspiranteManagementService->obtenerAspirantesPorProgramaId($programa);

        return view('complementarios.aspirantes.create', array_merge(
            $data,
            $this->obtenerDatosFormularioCreacion()
        ));
    }

    /** @return array<string, mixed> */
    private function obtenerDatosFormularioCreacion(): array
    {
        $temaTipoDocumento = $this->temaRepository->obtenerTiposDocumento();
        $tiposDocumento = $this->complementarioService->getTiposDocumento();
        $documentos = (object) [
            'tema' => $temaTipoDocumento,
            'parametros' => $temaTipoDocumento && $temaTipoDocumento->parametros->count() > 0
                ? $temaTipoDocumento->parametros()->where('parametros_temas.status', 1)->orderBy('parametros.name')->get(['parametros.id', 'parametros.name'])
                : $tiposDocumento,
        ];

        return [
            'documentos' => $documentos,
            'generos' => $this->resolverTemaConFallback($this->temaRepository->obtenerGeneros(), $this->complementarioService->getGeneros()),
            'caracterizaciones' => $this->resolverTemaConFallback($this->temaRepository->obtenerCaracterizacionesComplementarias(), collect()),
            'vias' => $this->resolverTemaConFallback($this->temaRepository->obtenerVias(), collect()),
            'letras' => $this->resolverTemaConFallback($this->temaRepository->obtenerLetras(), collect()),
            'cardinales' => $this->resolverTemaConFallback($this->temaRepository->obtenerCardinales(), collect()),
            'nivelEscolaridad' => $this->resolverTemaConFallback($this->temaRepository->obtenerNivelEscolaridad(), collect()),
            'paises' => Pais::all(),
            'departamentos' => Departamento::all(),
            'municipios' => collect(),
        ];
    }

    /** @param Collection<int, mixed>|mixed $fallback */
    private function resolverTemaConFallback(mixed $tema, mixed $fallback): object
    {
        if ($tema && isset($tema->parametros) && $tema->parametros->count() > 0) {
            return $tema;
        }

        return (object) ['parametros' => $fallback];
    }
}
