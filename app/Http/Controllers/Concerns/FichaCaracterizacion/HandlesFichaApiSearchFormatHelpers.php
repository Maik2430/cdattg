<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

trait HandlesFichaApiSearchFormatHelpers
{
    private function formatFichaForNumberSearchApi($ficha): array
    {
        return [
            'id' => $ficha->id,
            'numero_ficha' => $ficha->ficha,
            'fecha_inicio' => $ficha->fecha_inicio,
            'fecha_fin' => $ficha->fecha_fin,
            'total_horas' => $ficha->total_horas,
            'status' => $ficha->status,
            'programa_formacion' => [
                'id' => $ficha->programaFormacion->id ?? null,
                'nombre' => $ficha->programaFormacion->nombre ?? 'N/A',
                'codigo' => $ficha->programaFormacion->codigo ?? 'N/A',
                'nivel_formacion' => $ficha->programaFormacion->nivel_formacion ?? 'N/A',
            ],
            'instructor_principal' => [
                'id' => $ficha->instructor->id ?? null,
                'persona' => [
                    'id' => $ficha->instructor->persona->id ?? null,
                    'primer_nombre' => $ficha->instructor->persona->primer_nombre ?? 'N/A',
                    'segundo_nombre' => $ficha->instructor->persona->segundo_nombre ?? '',
                    'primer_apellido' => $ficha->instructor->persona->primer_apellido ?? 'N/A',
                    'segundo_apellido' => $ficha->instructor->persona->segundo_apellido ?? '',
                    'tipo_documento' => $ficha->instructor->persona->tipo_documento ?? 'N/A',
                    'numero_documento' => $ficha->instructor->persona->numero_documento ?? 'N/A',
                    'email' => $ficha->instructor->persona->email ?? 'N/A',
                    'telefono' => $ficha->instructor->persona->telefono ?? 'N/A',
                ],
            ],
            'jornada_formacion' => [
                'id' => $ficha->jornadaFormacion->id ?? null,
                'jornada' => $ficha->jornadaFormacion->parametro->name ?? 'N/A',
            ],
            'ambiente' => [
                'id' => $ficha->ambiente->id ?? null,
                'nombre' => $ficha->ambiente->title ?? 'N/A',
                'piso' => [
                    'id' => $ficha->ambiente->piso->id ?? null,
                    'piso' => $ficha->ambiente->piso->piso ?? 'N/A',
                    'bloque' => [
                        'id' => $ficha->ambiente->piso->bloque->id ?? null,
                        'nombre' => $ficha->ambiente->piso->bloque->bloque ?? 'N/A',
                    ] ?? null,
                ] ?? null,
            ] ?? null,
            'modalidad_formacion' => [
                'id' => $ficha->modalidadFormacion->id ?? null,
                'nombre' => $ficha->modalidadFormacion->name ?? 'N/A',
            ] ?? null,
            'sede' => [
                'id' => $ficha->sede->id ?? null,
                'sede' => $ficha->sede->sede ?? 'N/A',
                'direccion' => $ficha->sede->direccion ?? 'N/A',
            ] ?? null,
            'dias_formacion' => $ficha->diasFormacion->map(function ($dia) {
                return [
                    'id' => $dia->id,
                    'hora_inicio' => $dia->hora_inicio,
                    'hora_fin' => $dia->hora_fin,
                    'dia' => [
                        'id' => $dia->dia->id ?? null,
                        'nombre' => $dia->dia->name ?? 'N/A',
                    ] ?? null,
                ];
            }),
            'instructores_asignados' => $ficha->instructorFicha->map(function ($instructorFicha) {
                return [
                    'id' => $instructorFicha->id,
                    'fecha_inicio' => $instructorFicha->fecha_inicio,
                    'fecha_fin' => $instructorFicha->fecha_fin,
                    'total_horas_ficha' => $instructorFicha->total_horas_instructor,
                    'instructor' => [
                        'id' => $instructorFicha->instructor->id ?? null,
                        'persona' => [
                            'id' => $instructorFicha->instructor->persona->id ?? null,
                            'primer_nombre' => $instructorFicha->instructor->persona->primer_nombre ?? 'N/A',
                            'segundo_nombre' => $instructorFicha->instructor->persona->segundo_nombre ?? '',
                            'primer_apellido' => $instructorFicha->instructor->persona->primer_apellido ?? 'N/A',
                            'segundo_apellido' => $instructorFicha->instructor->persona->segundo_apellido ?? '',
                            'tipo_documento' => $instructorFicha->instructor->persona->tipo_documento ?? 'N/A',
                            'numero_documento' => $instructorFicha->instructor->persona->numero_documento ?? 'N/A',
                            'email' => $instructorFicha->instructor->persona->email ?? 'N/A',
                            'telefono' => $instructorFicha->instructor->persona->telefono ?? 'N/A',
                        ] ?? null,
                    ] ?? null,
                ];
            }),
            'created_at' => $ficha->created_at,
            'updated_at' => $ficha->updated_at,
        ];
    }
}
