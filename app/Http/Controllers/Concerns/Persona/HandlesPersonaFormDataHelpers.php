<?php

namespace App\Http\Controllers\Concerns\Persona;

use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Pais;
use App\Models\Persona;
use App\Models\Tema;
use Illuminate\Support\Collection;

trait HandlesPersonaFormDataHelpers
{
    /**
     * @return array<string, mixed>
     */
    protected function loadPersonaCreateFormData(): array
    {
        $documentos = $this->temaRepo->obtenerTiposDocumento();
        $generos = $this->temaRepo->obtenerGeneros();
        $paises = Pais::where('status', 1)->get();
        $departamentos = Departamento::where('status', 1)->get();
        $municipios = Municipio::where('status', 1)->get();
        $vias = $this->temaRepo->obtenerVias();
        $letras = $this->temaRepo->obtenerLetras();
        $cardinales = $this->temaRepo->obtenerCardinales();
        $caracterizaciones = Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->findOrFail(16);

        return compact(
            'documentos',
            'generos',
            'paises',
            'departamentos',
            'municipios',
            'caracterizaciones',
            'vias',
            'letras',
            'cardinales'
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function loadPersonaEditFormData(Persona $persona): array
    {
        $documentos = Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->findOrFail(2);

        $generos = Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->findOrFail(3);

        $paises = Pais::where('status', 1)->get();
        $departamentos = Departamento::where('status', 1)->get();
        $municipios = Collection::make([]);
        $caracterizaciones = Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->findOrFail(16);

        $vias = $this->temaRepo->obtenerVias();
        $cardinales = $this->temaRepo->obtenerCardinales();

        return [
            'persona' => $persona,
            'documentos' => $documentos,
            'generos' => $generos,
            'paises' => $paises,
            'departamentos' => $departamentos,
            'municipios' => $municipios,
            'caracterizaciones' => $caracterizaciones,
            'vias' => $vias,
            'letras' => $this->temaRepo->obtenerLetras(),
            'cardinales' => $cardinales,
        ];
    }
}
