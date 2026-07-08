<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaDestroyActions
{
    public function destroy(string $id)
    {
        try {
            $user = Auth::user();

            Log::info('═══════════════════════════════════════════════════════════');
            Log::info('INICIO: Intento de eliminación de ficha de caracterización', [
                'ficha_id' => $id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_roles' => $user->roles->pluck('name')->toArray(),
                'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);

            Log::info('Ficha encontrada', [
                'ficha_id' => $ficha->id,
                'numero_ficha' => $ficha->ficha,
                'programa_formacion_id' => $ficha->programa_formacion_id,
                'programa_nombre' => $ficha->programaFormacion->nombre ?? 'N/A',
                'instructor_id' => $ficha->instructor_id,
                'sede_id' => $ficha->sede_id,
                'status' => $ficha->status ? 'Activa' : 'Inactiva',
                'fecha_inicio' => $ficha->fecha_inicio ? $ficha->fecha_inicio->format('Y-m-d') : null,
                'fecha_fin' => $ficha->fecha_fin ? $ficha->fecha_fin->format('Y-m-d') : null,
            ]);

            if (! $this->fichaDestroyResolveAuthorization($user, $ficha, $id)) {
                Log::error('✗ ACCESO DENEGADO: Usuario no autorizado para eliminar fichas', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_roles' => $user->roles->pluck('name')->toArray(),
                    'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                    'ficha_id' => $id,
                ]);

                return redirect()->route('fichaCaracterizacion.index')
                    ->with('error', 'No tienes permisos para eliminar fichas de caracterización.');
            }

            $validationResponse = $this->fichaDestroyValidateDeletion($ficha, $id, $user);
            if ($validationResponse !== null) {
                return $validationResponse;
            }

            return $this->fichaDestroyExecuteDeletion($ficha, $id, $user);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('═══════════════════════════════════════════════════════════');
            Log::error('✗ ERROR: Ficha de caracterización no encontrada', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ]);
            Log::error('═══════════════════════════════════════════════════════════');

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'La ficha de caracterización solicitada no existe.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('═══════════════════════════════════════════════════════════');
            Log::error('✗ ERROR: Acceso denegado - Sin autorización para eliminar', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email ?? 'N/A',
                'user_roles' => Auth::user()->roles->pluck('name')->toArray() ?? [],
                'error' => 'El usuario no tiene los permisos necesarios para eliminar fichas',
                'required_permission' => 'ELIMINAR FICHA CARACTERIZACION',
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ]);
            Log::error('═══════════════════════════════════════════════════════════');

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'No tienes permisos para eliminar fichas de caracterización.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('═══════════════════════════════════════════════════════════');
            Log::error('✗ ERROR GENERAL al eliminar ficha de caracterización', [
                'ficha_id' => $id,
                'error_message' => $e->getMessage(),
                'error_type' => get_class($e),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ]);
            Log::error('═══════════════════════════════════════════════════════════');

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'Ocurrió un error al eliminar la ficha de caracterización. Por favor, intente nuevamente. Error: '.$e->getMessage());
        }
    }
}
