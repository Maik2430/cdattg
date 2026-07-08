<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Http\Requests\CreateInstructorRequest;
use Illuminate\Support\Facades\Auth;

trait HandlesInstructorStoreDataHelpers
{
    /**
     * @return array<string, mixed>
     */
    protected function buildInstructorStoreData(CreateInstructorRequest $request): array
    {
        $datos = $request->validated();
        $datos['especialidades'] = $this->parseIdArrayFromRequest($request, 'especialidades');
        $datos = $this->appendStoreJsonFields($request, $datos);
        $datos['user_create_id'] = Auth::id();

        return $datos;
    }

    /**
     * @return list<int|string>
     */
    protected function parseJornadasFromRequest(CreateInstructorRequest $request): array
    {
        return $this->parseIdArrayFromRequest($request, 'jornadas');
    }

    /**
     * @return list<int|string>
     */
    private function parseIdArrayFromRequest(CreateInstructorRequest $request, string $field): array
    {
        if ($request->has($field) && is_array($request->input($field))) {
            return array_values(array_filter($request->input($field, [])));
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $datos
     * @return array<string, mixed>
     */
    private function appendStoreJsonFields(CreateInstructorRequest $request, array $datos): array
    {
        foreach ($this->storeJsonFieldNames() as $campo) {
            if ($request->has($campo) && is_array($request->input($campo, []))) {
                $valores = array_filter(array_map('trim', $request->input($campo, [])));
                $datos[$campo] = $valores !== [] ? array_values($valores) : null;
            } else {
                $datos[$campo] = null;
            }
        }

        return $datos;
    }

    /**
     * @return list<string>
     */
    private function storeJsonFieldNames(): array
    {
        return [
            'titulos_obtenidos',
            'instituciones_educativas',
            'certificaciones_tecnicas',
            'cursos_complementarios',
            'areas_experticia',
            'competencias_tic',
            'idiomas',
            'habilidades_pedagogicas',
            'documentos_adjuntos',
        ];
    }
}
