<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario\Concerns;

use App\Models\Departamento;
use App\Models\Inventario\Proveedor;
use App\Models\Municipio;
use App\Models\Pais;
use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

trait HandlesProveedorFormActions
{
    public function create(): View
    {
        return view('inventario.proveedores.create', $this->buildFormData());
    }

    public function edit(Proveedor $proveedor): View
    {
        $data = $this->buildFormData($proveedor->persona_id);
        $data['proveedor'] = $proveedor;

        return view('inventario.proveedores.edit', $data);
    }

    public function getDepartamentosPorPais(int $paisId): JsonResponse
    {
        try {
            $departamentos = Departamento::where('pais_id', $paisId)
                ->orderBy('departamento')
                ->get(['id', 'departamento']);

            return response()->json($departamentos);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener los departamentos',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getMunicipiosPorDepartamento(int $departamentoId): JsonResponse
    {
        try {
            $municipios = Municipio::where('departamento_id', $departamentoId)
                ->orderBy('municipio')
                ->get(['id', 'municipio']);

            return response()->json($municipios);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener los municipios',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @return array{paises: mixed, departamentos: mixed, municipios: mixed, personas: mixed}
     */
    private function buildFormData(?int $selectedPersonaId = null): array
    {
        $personas = Persona::where('status', 1)
            ->where(function ($query) use ($selectedPersonaId): void {
                $query->whereHas('user.roles', function ($roleQuery): void {
                    $roleQuery->where('name', 'PROVEEDOR');
                });

                if ($selectedPersonaId !== null) {
                    $query->orWhere('id', $selectedPersonaId);
                }
            })
            ->orderBy('primer_nombre')
            ->orderBy('primer_apellido')
            ->get();

        return [
            'paises' => Pais::where('status', 1)->orderBy('pais')->get(),
            'departamentos' => Departamento::orderBy('departamento')->get(),
            'municipios' => Municipio::with('departamento')->get(),
            'personas' => $personas,
        ];
    }
}
