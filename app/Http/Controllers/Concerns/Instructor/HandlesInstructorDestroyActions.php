<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorDestroyActions
{
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instructor $instructor)
    {
        try {
            $this->instructorService->eliminar($instructor->id);

            return redirect()
                ->route('instructor.index')
                ->with('success', 'Instructor eliminado exitosamente.');
        } catch (QueryException $e) {
            Log::error('Error al eliminar instructor: '.$e->getMessage());

            if ($e->getCode() == 23000) {
                return redirect()
                    ->back()
                    ->with('error', 'El instructor se encuentra en uso, no se puede eliminar');
            }

            return redirect()
                ->back()
                ->with('error', 'Error de base de datos al eliminar el instructor.');
        } catch (Exception $e) {
            Log::error('Error al eliminar instructor: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function deleteWithoudUser($id)
    {
        try {
            DB::beginTransaction();

            // Buscar el instructor por persona_id
            $instructor = Instructor::where('persona_id', $id)->first();

            if (! $instructor) {
                return redirect()
                    ->back()
                    ->with('error', 'No se encontró el instructor.');
            }

            // Eliminar solo el instructor
            $instructor->delete();

            DB::commit();

            Log::info('Instructor sin usuario eliminado exitosamente', [
                'instructor_id' => $instructor->id,
                'persona_id' => $id,
            ]);

            return redirect()
                ->back()
                ->with('success', 'Instructor eliminado exitosamente. La persona se mantiene intacta.');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar instructor sin usuario', [
                'persona_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al eliminar el instructor.');
        }
    }
}
