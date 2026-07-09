<?php

namespace App\Services\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorCreacionActions
{
    /**
     * Crea un nuevo instructor
     *
     * @param  array<string, mixed>  $datos
     *
     * @throws \Exception
     */
    public function crear(array $datos, array $jornadasIds = [], array $modalidadesIds = []): Instructor
    {
        return DB::transaction(function () use ($datos, $jornadasIds, $modalidadesIds) {
            $persona = Persona::with(['instructor', 'user'])->findOrFail($datos['persona_id']);

            if ($persona->instructor) {
                throw new \Exception('Esta persona ya es instructor.');
            }

            if (! $persona->user) {
                throw new \Exception('Esta persona no tiene un usuario asociado.');
            }

            $datosInstructor = $this->prepararDatosInstructorCreacion($persona->id, $datos);
            $instructor = Instructor::create($datosInstructor);

            if (! empty($datos['especialidades'])) {
                $this->asignarEspecialidades($instructor, $datos['especialidades']);
            }

            $this->sincronizarJornadasInstructorCreacion($instructor, $jornadasIds);
            $this->sincronizarModalidadesInstructorCreacion($instructor, $modalidadesIds);

            $persona->user->syncRoles(['INSTRUCTOR']);

            Log::info('Instructor creado exitosamente', [
                'instructor_id' => $instructor->id,
                'persona_id' => $persona->id,
                'user_id' => $persona->user->id,
            ]);

            return $instructor;
        });
    }
}
