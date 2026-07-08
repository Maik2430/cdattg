<?php

namespace App\Services\Concerns\PersonaImport;

use App\Services\PersonaImportNormalizer;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaImportHeaderHelpers
{
    private function resolverEncabezados(array $row): array
    {
        $map = [];

        $headerTargets = [
            'tipo_documento' => [
                'tipo de documento',
                'tipodocumento',
                'tipo documento',
                'tipo_documento',
                'tipo documento identidad',
                'documento tipo',
                'td',
                'tipo doc',
            ],
            'numero_documento' => [
                'numero de documento',
                'número de documento',
                'numero documento',
                'documento',
                'documento identidad',
            ],
            'primer_nombre' => [
                'primer nombre',
                'nombre',
                'nombre1',
            ],
            'segundo_nombre' => [
                'segundo nombre',
                'nombre2',
            ],
            'primer_apellido' => [
                'primer apellido',
                'apellido',
                'apellido1',
            ],
            'segundo_apellido' => [
                'segundo apellido',
                'apellido2',
            ],
            'email' => [
                'correo electronico',
                'correo electrónico',
                'correo',
                'email',
            ],
            'celular' => [
                'celular',
                'telefono celular',
                'teléfono celular',
                'movil',
                'móvil',
            ],
            'telefono' => [
                'telefono',
                'teléfono',
                'telefono fijo',
                'teléfono fijo',
            ],
        ];

        foreach ($row as $column => $value) {
            $normalized = PersonaImportNormalizer::normalizarTexto($value ?? '');

            foreach ($headerTargets as $field => $targets) {
                if (in_array($normalized, $targets, true)) {
                    $map[$field] = $column;
                    Log::debug('Encabezado mapeado', [
                        'campo' => $field,
                        'columna' => $column,
                        'valor_original' => $value,
                        'valor_normalizado' => $normalized,
                    ]);
                    break;
                }
            }
        }

        $requeridos = ['tipo_documento', 'numero_documento', 'primer_nombre', 'primer_apellido'];

        foreach ($requeridos as $campo) {
            if (! array_key_exists($campo, $map)) {
                Log::warning('Encabezado requerido no encontrado', [
                    'campo_faltante' => $campo,
                    'encabezados_disponibles' => array_map(
                        fn ($v) => PersonaImportNormalizer::normalizarTexto($v ?? ''),
                        $row
                    ),
                ]);

                return [];
            }
        }

        Log::info('Mapeo de encabezados completado', ['map' => $map]);

        return $map;
    }
}
