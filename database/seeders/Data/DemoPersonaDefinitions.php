<?php

namespace Database\Seeders\Data;

/**
 * Punto de entrada del catálogo de personas demo (IDs 1–8).
 */
final class DemoPersonaDefinitions
{
    /**
     * @return list<array{id: int, attributes: array<string, mixed>}>
     */
    public static function all(?int $tipoDocumentoCedula, ?int $generoMasculino, ?int $generoFemenino): array
    {
        return array_merge(
            DemoPersonaStaffDefinitions::all($tipoDocumentoCedula, $generoMasculino, $generoFemenino),
            DemoPersonaRoleDefinitions::all($tipoDocumentoCedula, $generoMasculino, $generoFemenino),
        );
    }
}
