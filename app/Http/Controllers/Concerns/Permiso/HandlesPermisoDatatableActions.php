<?php

namespace App\Http\Controllers\Concerns\Permiso;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait HandlesPermisoDatatableActions
{
    use HandlesPermisoDatatableHelpers;

    public function datatable(Request $request): JsonResponse
    {
        $baseQuery = User::query()->with(['persona', 'roles']);

        $recordsTotal = (clone $baseQuery)->count();

        $filteredQuery = clone $baseQuery;

        $searchValue = $request->input('search.value');
        $this->applyPermisoDatatableSearch($filteredQuery, $searchValue);

        $recordsFiltered = $searchValue ? (clone $filteredQuery)->count() : $recordsTotal;

        $columns = $this->getPermisoDatatableColumns();

        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'asc');
        $orderColumn = $columns[$orderColumnIndex] ?? 'users.id';

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $usersQuery = (clone $filteredQuery)->leftJoin('personas', 'users.persona_id', '=', 'personas.id')->orderBy($orderColumn, $orderDirection)->select('users.*');

        if ($length !== -1) {
            $usersQuery->skip($start)->take($length);
        }

        $users = $usersQuery->get()->load('persona');

        $data = $users->map(function (User $user, $index) use ($start) {
            return $this->buildPermisoDatatableRow($user, $index, $start);
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}
