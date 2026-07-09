<?php

namespace App\Http\Controllers\Concerns\Persona;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Builder;

trait HandlesPersonaDatatableHelpers
{
    /**
     * @param  Builder<Persona>  $query
     */
    protected function applyPersonaDatatableSearch(Builder $query, ?string $searchValue): void
    {
        if (! $searchValue) {
            return;
        }

        $query->where(function ($innerQuery) use ($searchValue) {
            $innerQuery->where('primer_nombre', 'like', "%{$searchValue}%")
                ->orWhere('segundo_nombre', 'like', "%{$searchValue}%")
                ->orWhere('primer_apellido', 'like', "%{$searchValue}%")
                ->orWhere('segundo_apellido', 'like', "%{$searchValue}%")
                ->orWhere('numero_documento', 'like', "%{$searchValue}%")
                ->orWhere('email', 'like', "%{$searchValue}%");
        });
    }

    /**
     * @param  Builder<Persona>  $query
     * @return array{estado_aplicado: bool}
     */
    protected function applyPersonaDatatableEstadoFilter(Builder $query, mixed $estado): array
    {
        if (! $estado || $estado === 'todos') {
            return ['estado_aplicado' => false];
        }

        $query->where('status', $estado === 'activos' ? 1 : 0);

        return ['estado_aplicado' => true];
    }

    /**
     * @return array<int, string>
     */
    protected function getPersonaDatatableColumns(): array
    {
        return [
            0 => 'id',
            1 => 'primer_nombre',
            2 => 'numero_documento',
            3 => 'email',
            4 => 'telefono',
            5 => 'celular',
            6 => 'status',
            7 => 'estado_sofia',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildPersonaDatatableRow(Persona $persona, int $index, int $start): array
    {
        $badgeNoRegistrado = '<span class="badge bg-danger text-white px-2 py-1">No registrado</span>';
        $email = $persona->email ? e($persona->email) : $badgeNoRegistrado;
        $celular = $persona->celular
            ? '<a href="https://wa.me/'.e($persona->celular).'" target="_blank" class="text-decoration-none">'
            .e($persona->celular).' <i class="fab fa-whatsapp text-success"></i></a>'
            : $badgeNoRegistrado;

        return [
            'index' => $start + $index + 1,
            'nombre' => $persona->nombre_completo,
            'numero_documento' => $persona->numero_documento,
            'email' => $email,
            'celular' => $celular,
            'estado' => view('personas.partials.estado', ['persona' => $persona])->render(),
            'estado_sofia' => view('personas.partials.estado-sofia', ['persona' => $persona])->render(),
            'acciones' => view('personas.partials.acciones', ['persona' => $persona])->render(),
        ];
    }
}
