<?php

namespace Database\Seeders\Data;

/**
 * Personas demo operativas: aprendices y proveedor (IDs 6–8).
 */
final class DemoPersonaRoleDefinitions
{
    /**
     * @return list<array{id: int, attributes: array<string, mixed>}>
     */
    public static function all(?int $tipoDocumentoCedula, ?int $generoMasculino, ?int $generoFemenino): array
    {
        return [
            self::aprendizUno($tipoDocumentoCedula, $generoFemenino),
            self::aprendizDos($tipoDocumentoCedula, $generoMasculino),
            self::proveedor($tipoDocumentoCedula, $generoMasculino),
        ];
    }

    private static function aprendizUno(?int $tipoDocumento, ?int $genero): array
    {
        return DemoPersonaRecordFactory::make(6, $tipoDocumento, $genero, [
            'numero_documento' => 444444444,
            'primer_nombre' => 'APRENDIZ',
            'segundo_nombre' => 'UNO',
            'primer_apellido' => 'CDATTG',
            'segundo_apellido' => 'PRUEBAS',
            'fecha_nacimiento' => '2002-03-20',
            'celular' => '3034444444',
            'email' => 'aprendiz1@dataguaviare.com',
            'direccion' => 'AVENIDA 5 #22-10',
        ]);
    }

    private static function aprendizDos(?int $tipoDocumento, ?int $genero): array
    {
        return DemoPersonaRecordFactory::make(7, $tipoDocumento, $genero, [
            'numero_documento' => 333333333,
            'primer_nombre' => 'APRENDIZ',
            'segundo_nombre' => 'DOS',
            'primer_apellido' => 'CDATTG',
            'segundo_apellido' => 'PRUEBAS',
            'fecha_nacimiento' => '2003-07-05',
            'celular' => '3043333333',
            'email' => 'aprendiz2@dataguaviare.com',
            'direccion' => 'AVENIDA 6 #18-20',
        ]);
    }

    private static function proveedor(?int $tipoDocumento, ?int $genero): array
    {
        return DemoPersonaRecordFactory::make(8, $tipoDocumento, $genero, [
            'numero_documento' => 222222222,
            'primer_nombre' => 'PROVEEDOR',
            'segundo_nombre' => 'DEMO',
            'primer_apellido' => 'CDATTG',
            'segundo_apellido' => 'PRUEBAS',
            'fecha_nacimiento' => '1988-05-15',
            'celular' => '3052222222',
            'email' => 'proveedor@dataguaviare.com',
            'direccion' => 'CALLE 7 #14-25',
        ]);
    }
}
