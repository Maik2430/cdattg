<?php

namespace App\Repositories;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Collection;

class PersonaRepository
{
    /**
     * Buscar persona por número de documento
     */
    public function findByNumeroDocumento(string $numeroDocumento): ?Persona
    {
        return Persona::where('numero_documento', $numeroDocumento)->first();
    }

    /**
     * Buscar persona por email
     */
    public function findByEmail(string $email): ?Persona
    {
        return Persona::where('email', $email)->first();
    }

    /**
     * Buscar persona por documento o email
     */
    public function findByDocumentoOrEmail(string $numeroDocumento, string $email): ?Persona
    {
        return Persona::where('numero_documento', $numeroDocumento)
            ->orWhere('email', $email)
            ->first();
    }

    /**
     * Verificar si existe persona con documento o email
     */
    public function existsByDocumentoOrEmail(string $numeroDocumento, string $email): bool
    {
        return Persona::where('numero_documento', $numeroDocumento)
            ->orWhere('email', $email)
            ->exists();
    }

    /**
     * Crear nueva persona
     */
    public function create(array $data): Persona
    {
        // Agregar datos de auditoría por defecto
        $data = array_merge($data, [
            'user_create_id' => auth()->id() ?? 1,
            'user_edit_id' => auth()->id() ?? 1,
        ]);

        return Persona::create($data);
    }

    /**
     * Actualizar persona
     */
    public function update(Persona $persona, array $data): bool
    {
        // Agregar datos de auditoría
        $data['user_edit_id'] = auth()->id() ?? 1;

        return $persona->update($data);
    }

    /**
     * Crear o actualizar persona
     */
    public function createOrUpdate(array $data): Persona
    {
        $persona = $this->findByDocumentoOrEmail(
            $data['numero_documento'],
            $data['email']
        );

        if ($persona) {
            $this->update($persona, $data);
            return $persona->fresh();
        }

        return $this->create($data);
    }

    /**
     * Obtener personas con caracterización
     */
    public function getAllWithCaracterizacion(): Collection
    {
        return Persona::with(['caracterizacion', 'tipoDocumento'])->get();
    }

    /**
     * Buscar personas por criterios múltiples
     */
    public function search(array $criteria): Collection
    {
        $query = Persona::query();

        if (isset($criteria['departamento_id'])) {
            $query->where('departamento_id', $criteria['departamento_id']);
        }

        if (isset($criteria['municipio_id'])) {
            $query->where('municipio_id', $criteria['municipio_id']);
        }

        if (isset($criteria['genero'])) {
            $query->where('genero', $criteria['genero']);
        }

        if (isset($criteria['caracterizacion_id'])) {
            $query->where('caracterizacion_id', $criteria['caracterizacion_id']);
        }

        return $query->get();
    }

    /**
     * Actualizar estado de documento
     */
    public function updateDocumentoStatus(Persona $persona, bool $tieneDocumento): bool
    {
        return $persona->update(['condocumento' => $tieneDocumento ? 1 : 0]);
    }

    /**
     * Obtener estadísticas por género
     */
    public function getEstadisticasPorGenero(): Collection
    {
        return Persona::selectRaw('
                parametros.name as genero,
                COUNT(*) as total
            ')
            ->join('parametros', 'personas.genero', '=', 'parametros.id')
            ->groupBy('personas.genero', 'parametros.name')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Obtener estadísticas por rango de edad
     */
    public function getEstadisticasPorEdad(): Collection
    {
        return Persona::selectRaw('
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) < 18 THEN "Menor de 18"
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 18 AND 25 THEN "18-25 años"
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 26 AND 35 THEN "26-35 años"
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 36 AND 45 THEN "36-45 años"
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 46 AND 55 THEN "46-55 años"
                    ELSE "Mayor de 55"
                END as rango_edad,
                COUNT(*) as total
            ')
            ->groupByRaw('CASE
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) < 18 THEN "Menor de 18"
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 18 AND 25 THEN "18-25 años"
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 26 AND 35 THEN "26-35 años"
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 36 AND 45 THEN "36-45 años"
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 46 AND 55 THEN "46-55 años"
                ELSE "Mayor de 55"
            END')
            ->orderBy('total', 'desc')
            ->get();
    }
}