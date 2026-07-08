<?php

namespace Database\Seeders\Data;

/**
 * Personas demo de roles administrativos e instructivos (IDs 1–5).
 */
final class DemoPersonaStaffDefinitions
{
    /**
     * @return list<array{id: int, attributes: array<string, mixed>}>
     */
    public static function all(?int $tipoDocumentoCedula, ?int $generoMasculino, ?int $generoFemenino): array
    {
        return [
            self::bot($tipoDocumentoCedula, $generoMasculino),
            self::superAdministrador($tipoDocumentoCedula, $generoMasculino),
            self::administrador($tipoDocumentoCedula, $generoFemenino),
            self::coordinador($tipoDocumentoCedula, $generoMasculino),
            self::instructor($tipoDocumentoCedula, $generoMasculino),
        ];
    }

    private static function bot(?int $tipoDocumento, ?int $genero): array
    {
        return DemoPersonaRecordFactory::make(1, $tipoDocumento, $genero, [
            'numero_documento' => 111111111,
            'primer_nombre' => 'BOT',
            'segundo_nombre' => null,
            'primer_apellido' => 'AUTOMATICO',
            'segundo_apellido' => null,
            'fecha_nacimiento' => '2000-01-01',
            'celular' => '3001111111',
            'email' => 'bot@dataguaviare.com',
            'direccion' => 'CALLE 11 #11-11',
        ]);
    }

    private static function superAdministrador(?int $tipoDocumento, ?int $genero): array
    {
        return DemoPersonaRecordFactory::make(2, $tipoDocumento, $genero, [
            'numero_documento' => 987654321,
            'primer_nombre' => 'SUPER',
            'segundo_nombre' => null,
            'primer_apellido' => 'ADMINISTRADOR',
            'segundo_apellido' => null,
            'fecha_nacimiento' => '1980-01-01',
            'celular' => '3000000000',
            'email' => 'superadmin@dataguaviare.com',
            'direccion' => 'CALLE 10 #10-10',
        ]);
    }

    private static function administrador(?int $tipoDocumento, ?int $genero): array
    {
        return DemoPersonaRecordFactory::make(3, $tipoDocumento, $genero, [
            'numero_documento' => 654321123,
            'primer_nombre' => 'ADMINISTRADOR',
            'segundo_nombre' => 'DEMO',
            'primer_apellido' => 'CDATTG',
            'segundo_apellido' => 'ADMIN',
            'fecha_nacimiento' => '1990-06-15',
            'celular' => '3010000000',
            'email' => 'admin@dataguaviare.com',
            'direccion' => 'CARRERA 8 #12-34',
        ]);
    }

    private static function coordinador(?int $tipoDocumento, ?int $genero): array
    {
        return DemoPersonaRecordFactory::make(4, $tipoDocumento, $genero, [
            'numero_documento' => 555125555,
            'primer_nombre' => 'COORDINADOR',
            'segundo_nombre' => 'DEMO',
            'primer_apellido' => 'CDATTG',
            'segundo_apellido' => 'PRUEBAS',
            'fecha_nacimiento' => '1985-04-10',
            'celular' => '3021255555',
            'email' => 'coordinador@dataguaviare.com',
            'direccion' => 'CALLE 12 #13-56',
        ]);
    }

    private static function instructor(?int $tipoDocumento, ?int $genero): array
    {
        return DemoPersonaRecordFactory::make(5, $tipoDocumento, $genero, [
            'numero_documento' => 555555555,
            'primer_nombre' => 'INSTRUCTOR',
            'segundo_nombre' => 'DEMO',
            'primer_apellido' => 'CDATTG',
            'segundo_apellido' => 'PRUEBAS',
            'fecha_nacimiento' => '1985-04-10',
            'celular' => '3025555555',
            'email' => 'instructor@dataguaviare.com',
            'direccion' => 'CALLE 12 #13-56',
        ]);
    }
}
