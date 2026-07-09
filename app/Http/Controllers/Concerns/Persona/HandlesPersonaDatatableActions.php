<?php

namespace App\Http\Controllers\Concerns\Persona;

use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait HandlesPersonaDatatableActions
{
    use HandlesPersonaDatatableHelpers;

    public function datatable(Request $request): JsonResponse
    {
        $this->authorize('VER PERSONA');

        $baseQuery = Persona::query()->with(['user']);
        $recordsTotal = (clone $baseQuery)->count();
        $filteredQuery = clone $baseQuery;

        $searchValue = $request->input('search.value');
        $this->applyPersonaDatatableSearch($filteredQuery, $searchValue);

        $estado = $request->input('estado');
        $estadoFilter = $this->applyPersonaDatatableEstadoFilter($filteredQuery, $estado);
        $estadoAplicado = $estadoFilter['estado_aplicado'];

        $requiresFiltering = $searchValue || $estadoAplicado;
        $recordsFiltered = $requiresFiltering ? (clone $filteredQuery)->count() : $recordsTotal;

        $registradosSofiaTotal = (clone $baseQuery)->where('estado_sofia', 278)->count();
        $registradosSofiaFiltrados = (clone $filteredQuery)->where('estado_sofia', 278)->count();

        $columns = $this->getPersonaDatatableColumns();
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'asc');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $personasQuery = (clone $filteredQuery)->orderBy($orderColumn, $orderDirection);

        if ($length !== -1) {
            $personasQuery->skip($start)->take($length);
        }

        $personas = $personasQuery->get();

        $data = $personas->map(function (Persona $persona, $index) use ($start) {
            return $this->buildPersonaDatatableRow($persona, $index, $start);
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'sofia_registrados_total' => $registradosSofiaTotal,
            'sofia_registrados_filtrados' => $registradosSofiaFiltrados,
            'total_general' => $recordsTotal,
            'total_filtrado' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}
