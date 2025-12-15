<?php

declare(strict_types=1);

namespace Tests\Modulos\Inventario\Feature\Requests\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder minimal for Inventario Request Tests.
 * Creates only the essential data needed for request validation tests.
 */
class InventarioRequestTestSeeder extends Seeder
{
    // Status values
    private const STATUS_ACTIVE = 1;
    private const STATUS_INACTIVE = 0;
    
    // Test user data
    private const TEST_USER_EMAIL = 'test@test.com';
    private const TEST_USER_PASSWORD = 'password';
    private const TEST_PERSONA_NUMERO_DOCUMENTO = '1234567890';
    private const TEST_PERSONA_PRIMER_NOMBRE = 'TEST';
    private const TEST_PERSONA_PRIMER_APELLIDO = 'USER';
    
    // Location names
    private const PAIS_NAME = 'COLOMBIA';
    private const DEPARTAMENTO_NAME = 'GUAVIARE';
    private const MUNICIPIO_NAME = 'SAN JOSE DEL GUAVIARE';
    private const REGIONAL_NAME = 'GUAVIARE';
    
    // Infrastructure names
    private const SEDE_NAME = 'CENTRO';
    private const SEDE_DIRECCION = 'Dirección test';
    private const BLOQUE_NAME = 'BLOQUE A';
    private const PISO_NAME = 'PISO 1';
    private const AMBIENTE_TITLE = 'AULA 101';
    
    /**
     * Get common timestamps array.
     */
    private function getTimestamps(): array
    {
        return [
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    /**
     * Get common status field.
     */
    private function getStatusField(): array
    {
        return ['status' => self::STATUS_ACTIVE];
    }
    
    /**
     * Get common user fields for creation/editing.
     */
    private function getUserFields(int $userId): array
    {
        return [
            'user_create_id' => $userId,
            'user_edit_id' => $userId,
        ];
    }
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $connection = DB::connection();
        $isSqlite = $connection->getDriverName() === 'sqlite';
        
        if ($isSqlite) {
            $connection->statement('PRAGMA foreign_keys = OFF');
        }
        
        // Create User and Persona (required for user_create_id fields)
        $userId = $this->crearUsuarioMinimo();
        
        // Create location hierarchy: Pais -> Departamento -> Municipio
        $paisId = $this->crearObtenerPais();
        $departamentoId = $this->crearObtenerDepartamento($paisId);
        $municipioId = $this->crearObtenerMunicipio($departamentoId);
        
        // Create Regional
        $regionalId = $this->crearObtenerRegional($userId);
        
        // Create infrastructure: Sede -> Bloque -> Piso -> Ambiente
        $sedeId = $this->crearObtenerSede($municipioId, $regionalId, $userId);
        $bloqueId = $this->crearObtenerBloque($sedeId, $userId);
        $pisoId = $this->crearObtenerPiso($bloqueId, $userId);
        $this->crearObtenerAmbiente($pisoId, $userId);
        
        // Note: Temas and Parametros are created by TemaSeeder and ParametroSeeder
        // Tests should use existing temas/parametros or create them on-demand
        
        if ($isSqlite) {
            $connection->statement('PRAGMA foreign_keys = ON');
        }
    }
    
    /**
     * Create minimal user with persona.
     */
    private function crearUsuarioMinimo(): int
    {
        $existingUserId = DB::table('users')->value('id');
        if ($existingUserId) {
            return $existingUserId;
        }
        
        // Create Persona first
        $personaId = DB::table('personas')->insertGetId(array_merge([
            'numero_documento' => self::TEST_PERSONA_NUMERO_DOCUMENTO,
            'primer_nombre' => self::TEST_PERSONA_PRIMER_NOMBRE,
            'primer_apellido' => self::TEST_PERSONA_PRIMER_APELLIDO,
            'email' => self::TEST_USER_EMAIL,
            'user_create_id' => null,
            'user_edit_id' => null,
        ], $this->getStatusField(), $this->getTimestamps()));
        
        // Create User
        $userId = DB::table('users')->insertGetId(array_merge([
            'email' => self::TEST_USER_EMAIL,
            'password' => bcrypt(self::TEST_USER_PASSWORD),
            'email_verified_at' => now(),
            'persona_id' => $personaId,
        ], $this->getStatusField(), $this->getTimestamps()));
        
        return $userId;
    }
    
    /**
     * Create or get Pais.
     */
    private function crearObtenerPais(): int
    {
        $paisId = DB::table('pais')->value('id');
        if (!$paisId) {
            $paisId = DB::table('pais')->insertGetId(array_merge([
                'pais' => self::PAIS_NAME,
            ], $this->getStatusField(), $this->getTimestamps()));
        }
        return $paisId;
    }
    
    /**
     * Create or get Departamento.
     */
    private function crearObtenerDepartamento(int $paisId): int
    {
        $departamentoId = DB::table('departamentos')->value('id');
        if (!$departamentoId) {
            $departamentoId = DB::table('departamentos')->insertGetId(array_merge([
                'departamento' => self::DEPARTAMENTO_NAME,
                'pais_id' => $paisId,
            ], $this->getStatusField(), $this->getTimestamps()));
        }
        return $departamentoId;
    }
    
    /**
     * Create or get Municipio.
     */
    private function crearObtenerMunicipio(int $departamentoId): int
    {
        $municipioId = DB::table('municipios')->value('id');
        if (!$municipioId) {
            $municipioId = DB::table('municipios')->insertGetId(array_merge([
                'municipio' => self::MUNICIPIO_NAME,
                'departamento_id' => $departamentoId,
            ], $this->getStatusField(), $this->getTimestamps()));
        }
        return $municipioId;
    }
    
    /**
     * Create or get Regional.
     */
    private function crearObtenerRegional(int $userId): int
    {
        $regionalId = DB::table('regionals')->value('id');
        if (!$regionalId) {
            $regionalId = DB::table('regionals')->insertGetId(array_merge([
                'nombre' => self::REGIONAL_NAME,
            ], $this->getUserFields($userId), $this->getStatusField(), $this->getTimestamps()));
        }
        return $regionalId;
    }
    
    /**
     * Create or get Sede.
     */
    private function crearObtenerSede(int $municipioId, int $regionalId, int $userId): int
    {
        $sedeId = DB::table('sedes')->value('id');
        if (!$sedeId) {
            $sedeId = DB::table('sedes')->insertGetId(array_merge([
                'sede' => self::SEDE_NAME,
                'direccion' => self::SEDE_DIRECCION,
                'municipio_id' => $municipioId,
                'regional_id' => $regionalId,
            ], $this->getUserFields($userId), $this->getStatusField(), $this->getTimestamps()));
        }
        return $sedeId;
    }
    
    /**
     * Create or get Bloque.
     */
    private function crearObtenerBloque(int $sedeId, int $userId): int
    {
        $bloqueId = DB::table('bloques')->where('sede_id', $sedeId)->value('id');
        if (!$bloqueId) {
            $bloqueId = DB::table('bloques')->insertGetId(array_merge([
                'bloque' => self::BLOQUE_NAME,
                'sede_id' => $sedeId,
            ], $this->getUserFields($userId), $this->getStatusField(), $this->getTimestamps()));
        }
        return $bloqueId;
    }
    
    /**
     * Create or get Piso.
     */
    private function crearObtenerPiso(int $bloqueId, int $userId): int
    {
        $pisoId = DB::table('pisos')->where('bloque_id', $bloqueId)->value('id');
        if (!$pisoId) {
            $pisoId = DB::table('pisos')->insertGetId(array_merge([
                'piso' => self::PISO_NAME,
                'bloque_id' => $bloqueId,
            ], $this->getUserFields($userId), $this->getStatusField(), $this->getTimestamps()));
        }
        return $pisoId;
    }
    
    /**
     * Create or get Ambiente.
     */
    private function crearObtenerAmbiente(int $pisoId, int $userId): int
    {
        $ambienteId = DB::table('ambientes')->where('piso_id', $pisoId)->value('id');
        if (!$ambienteId) {
            $ambienteId = DB::table('ambientes')->insertGetId(array_merge([
                'title' => self::AMBIENTE_TITLE,
                'piso_id' => $pisoId,
            ], $this->getUserFields($userId), $this->getStatusField(), $this->getTimestamps()));
        }
        return $ambienteId;
    }
    
}

