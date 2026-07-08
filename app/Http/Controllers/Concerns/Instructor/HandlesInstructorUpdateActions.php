<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Http\Requests\UpdateInstructorRequest;
use App\Models\Instructor;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorUpdateActions
{
    public function update(UpdateInstructorRequest $request, Instructor $instructor)
    {
        try {
            DB::beginTransaction();

            $datos = $request->validated();

            $this->prepareUpdateEspecialidades($request, $datos);
            $jornadasIds = $this->prepareUpdateJornadas($request, $datos);
            $modalidadesIds = $this->prepareUpdateModalidades($request);
            $this->prepareUpdateJsonFields($datos);
            $this->validateUpdateForeignKeyFields($datos);
            $datosActualizar = $this->buildInstructorUpdatePayload($instructor, $datos);

            $instructor->update($datosActualizar);

            $this->logInstructorAfterUpdate($instructor);
            $this->syncInstructorJornadasOnUpdate($instructor, $jornadasIds);
            $this->syncInstructorModalidadesOnUpdate($instructor, $modalidadesIds);

            DB::commit();

            Log::info('Instructor actualizado exitosamente', [
                'instructor_id' => $instructor->id,
                'persona_id' => $instructor->persona_id,
            ]);

            return redirect()
                ->route('instructor.index')
                ->with('success', '¡Instructor actualizado exitosamente!');
        } catch (QueryException $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();

            Log::error('Error al actualizar instructor - QueryException', [
                'instructor_id' => $instructor->id,
                'error' => $errorMessage,
                'error_code' => $errorCode,
                'sql_state' => $e->errorInfo[0] ?? null,
                'sql_code' => $e->errorInfo[1] ?? null,
                'sql_message' => $e->errorInfo[2] ?? null,
                'request_data' => $request->except(['password']),
                'datos_actualizar' => $datosActualizar ?? [],
                'trace' => $e->getTraceAsString(),
            ]);

            $mensajeError = 'Error de base de datos. Por favor, inténtelo de nuevo.';
            if (str_contains($errorMessage, 'foreign key constraint')) {
                $mensajeError = 'Error: Uno de los valores seleccionados no es válido. Verifique las relaciones (regional, centro, tipo vinculación, nivel académico).';
            } elseif (str_contains($errorMessage, 'Integrity constraint violation')) {
                $mensajeError = 'Error: Violación de integridad de datos. Verifique que todos los valores sean válidos.';
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $mensajeError);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar instructor - Exception', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->except(['password']),
                'datos_actualizar' => $datosActualizar ?? [],
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error inesperado: '.$e->getMessage());
        }
    }
}
