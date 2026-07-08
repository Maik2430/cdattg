<?php

namespace Database\Seeders\Data;

/**
 * Construye registros de persona demo con atributos geográficos compartidos.
 */
final class DemoPersonaRecordFactory
{
    /**
     * @param  array<string, mixed>  $overrides
     * @return array{id: int, attributes: array<string, mixed>}
     */
    public static function make(int $id, ?int $tipoDocumento, ?int $genero, array $overrides): array
    {
        return [
            'id' => $id,
            'attributes' => array_merge([
                'tipo_documento' => $tipoDocumento,
                'genero' => $genero,
                'telefono' => null,
                'pais_id' => 1,
                'departamento_id' => 95,
                'municipio_id' => 1,
                'status' => 1,
                'user_create_id' => null,
                'user_edit_id' => null,
            ], $overrides),
        ];
    }
}
