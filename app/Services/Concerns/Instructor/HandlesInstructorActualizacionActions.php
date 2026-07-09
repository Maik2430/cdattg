<?php

namespace App\Services\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorActualizacionActions
{
    /**
     * Actualiza un instructor
     *
     * @param  array<string, mixed>  $datos
     */
    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $instructor = Instructor::findOrFail($id);
            $persona = Persona::findOrFail($instructor->persona_id);

            if (isset($datos['persona'])) {
                $persona->update($datos['persona']);
            }

            $instructor->update([
                'regional_id' => $datos['regional_id'] ?? $instructor->regional_id,
                'centro_formacion_id' => $datos['centro_formacion_id'] ?? $instructor->centro_formacion_id,
                'tipo_vinculacion_id' => $datos['tipo_vinculacion_id'] ?? $instructor->tipo_vinculacion_id,
                'jornadas' => $datos['jornadas'] ?? $instructor->jornadas,
                'fecha_ingreso_sena' => $datos['fecha_ingreso_sena'] ?? $instructor->fecha_ingreso_sena,
                'anos_experiencia' => $datos['anos_experiencia'] ?? $instructor->anos_experiencia,
                'experiencia_instructor_meses' => $datos['experiencia_instructor_meses'] ?? $instructor->experiencia_instructor_meses,
                'experiencia_laboral' => $datos['experiencia_laboral'] ?? $instructor->experiencia_laboral,
                'nivel_academico_id' => $datos['nivel_academico_id'] ?? $instructor->nivel_academico_id,
                'formacion_pedagogia' => $datos['formacion_pedagogia'] ?? $instructor->formacion_pedagogia,
                'titulos_obtenidos' => $datos['titulos_obtenidos'] ?? $instructor->titulos_obtenidos,
                'instituciones_educativas' => $datos['instituciones_educativas'] ?? $instructor->instituciones_educativas,
                'certificaciones_tecnicas' => $datos['certificaciones_tecnicas'] ?? $instructor->certificaciones_tecnicas,
                'cursos_complementarios' => $datos['cursos_complementarios'] ?? $instructor->cursos_complementarios,
                'areas_experticia' => $datos['areas_experticia'] ?? $instructor->areas_experticia,
                'competencias_tic' => $datos['competencias_tic'] ?? $instructor->competencias_tic,
                'idiomas' => $datos['idiomas'] ?? $instructor->idiomas,
                'modalidades' => $datos['modalidades'] ?? $instructor->modalidades,
                'especialidades' => $datos['especialidades'] ?? $instructor->especialidades,
                'numero_contrato' => $datos['numero_contrato'] ?? $instructor->numero_contrato,
                'fecha_inicio_contrato' => $datos['fecha_inicio_contrato'] ?? $instructor->fecha_inicio_contrato,
                'fecha_fin_contrato' => $datos['fecha_fin_contrato'] ?? $instructor->fecha_fin_contrato,
                'supervisor_contrato' => $datos['supervisor_contrato'] ?? $instructor->supervisor_contrato,
                'user_edit_id' => $datos['user_edit_id'] ?? $instructor->user_edit_id,
            ]);

            if (isset($datos['persona']['email'])) {
                $user = User::where('persona_id', $persona->id)->first();
                if ($user) {
                    $user->update([
                        'email' => $datos['persona']['email'],
                        'password' => Hash::make($datos['persona']['numero_documento'] ?? $persona->numero_documento),
                    ]);
                }
            }

            Log::info('Instructor actualizado exitosamente', [
                'instructor_id' => $instructor->id,
            ]);

            return true;
        });
    }
}
