<?php

namespace Database\Seeders\Data;

/**
 * Catálogo de usuarios demo vinculados a PersonaSeeder (persona_id 1–8).
 */
final class DemoUserDefinitions
{
    /**
     * @return list<array{email: string, password: string, persona_id: int, role: string}>
     */
    public static function all(): array
    {
        return [
            [
                'email' => 'info@dataguaviare.com.co',
                'password' => 'Guaviare25.',
                'persona_id' => 1,
                'role' => 'BOT',
            ],
            [
                'email' => 'superadmin@dataguaviare.com',
                'password' => 'Guaviare25.',
                'persona_id' => 2,
                'role' => 'SUPER ADMINISTRADOR',
            ],
            [
                'email' => 'admin@dataguaviare.com',
                'password' => 'Guaviare25.',
                'persona_id' => 3,
                'role' => 'ADMINISTRADOR',
            ],
            [
                'email' => 'coordinador@dataguaviare.com',
                'password' => 'Guaviare25.',
                'persona_id' => 4,
                'role' => 'COORDINADOR',
            ],
            [
                'email' => 'instructor@dataguaviare.com',
                'password' => 'Guaviare25.',
                'persona_id' => 5,
                'role' => 'INSTRUCTOR',
            ],
            [
                'email' => 'aprendiz1@dataguaviare.com',
                'password' => 'Guaviare25!',
                'persona_id' => 6,
                'role' => 'APRENDIZ',
            ],
            [
                'email' => 'aprendiz2@dataguaviare.com',
                'password' => 'Guaviare25!',
                'persona_id' => 7,
                'role' => 'APRENDIZ',
            ],
            [
                'email' => 'proveedor@dataguaviare.com',
                'password' => 'Guaviare25.',
                'persona_id' => 8,
                'role' => 'PROVEEDOR',
            ],
        ];
    }
}
