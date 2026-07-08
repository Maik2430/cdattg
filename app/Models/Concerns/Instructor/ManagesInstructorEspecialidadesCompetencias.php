<?php

namespace App\Models\Concerns\Instructor;

trait ManagesInstructorEspecialidadesCompetencias
{
    /**
     * Agregar especialidad al instructor.
     */
    public function agregarEspecialidad(string $especialidad): void
    {
        $especialidades = $this->especialidades ?? [];
        if (! in_array($especialidad, $especialidades)) {
            $especialidades[] = $especialidad;
            $this->especialidades = $especialidades;
            $this->save();
        }
    }

    /**
     * Remover especialidad del instructor.
     */
    public function removerEspecialidad(string $especialidad): void
    {
        $especialidades = $this->especialidades ?? [];
        $especialidades = array_filter($especialidades, function ($esp) use ($especialidad) {
            return $esp !== $especialidad;
        });
        $this->especialidades = array_values($especialidades);
        $this->save();
    }

    /**
     * Agregar competencia al instructor.
     */
    public function agregarCompetencia(string $competencia): void
    {
        $competencias = $this->competencias ?? [];
        if (! in_array($competencia, $competencias)) {
            $competencias[] = $competencia;
            $this->competencias = $competencias;
            $this->save();
        }
    }

    /**
     * Remover competencia del instructor.
     */
    public function removerCompetencia(string $competencia): void
    {
        $competencias = $this->competencias ?? [];
        $competencias = array_filter($competencias, function ($comp) use ($competencia) {
            return $comp !== $competencia;
        });
        $this->competencias = array_values($competencias);
        $this->save();
    }

    /**
     * Verificar si el instructor tiene una especialidad específica.
     */
    public function tieneEspecialidad(string $especialidad): bool
    {
        return in_array($especialidad, $this->especialidades ?? []);
    }

    /**
     * Verificar si el instructor tiene una competencia específica.
     */
    public function tieneCompetencia(string $competencia): bool
    {
        return in_array($competencia, $this->competencias ?? []);
    }
}
