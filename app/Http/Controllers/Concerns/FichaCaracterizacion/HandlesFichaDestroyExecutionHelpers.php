<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaDestroyExecutionHelpers
{
    private function fichaDestroyExecuteDeletion(FichaCaracterizacion $ficha, string $id, $user): \Illuminate\Http\RedirectResponse
    {
        $fichaInfo = [
            'id' => $ficha->id,
            'numero_ficha' => $ficha->ficha,
            'programa_formacion_id' => $ficha->programa_formacion_id,
            'programa_nombre' => $ficha->programaFormacion->nombre ?? 'N/A',
            'instructor_id' => $ficha->instructor_id,
            'sede_id' => $ficha->sede_id,
            'sede_nombre' => $ficha->sede->nombre ?? 'N/A',
            'fecha_inicio' => $ficha->fecha_inicio ? $ficha->fecha_inicio->format('Y-m-d') : null,
            'fecha_fin' => $ficha->fecha_fin ? $ficha->fecha_fin->format('Y-m-d') : null,
            'created_at' => $ficha->created_at ? $ficha->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $ficha->updated_at ? $ficha->updated_at->format('Y-m-d H:i:s') : null,
        ];

        Log::info('Iniciando transacción de base de datos para eliminar la ficha');
        DB::beginTransaction();

        try {
            DB::table('ficha_dias_formacion')
                ->where('ficha_id', $id)
                ->delete();

            Log::info('✓ Días de formación eliminados correctamente', [
                'ficha_id' => $id,
            ]);

            $ficha->delete();

            DB::commit();

            Log::info('═══════════════════════════════════════════════════════════');
            Log::info('✓ ÉXITO: Ficha de caracterización eliminada correctamente', [
                'ficha_eliminada' => $fichaInfo,
                'eliminada_por_user_id' => $user->id,
                'eliminada_por_user_name' => $user->name,
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ]);
            Log::info('═══════════════════════════════════════════════════════════');

            return redirect()->route('fichaCaracterizacion.index')
                ->with('success', 'Ficha de caracterización #'.$fichaInfo['numero_ficha'].' eliminada exitosamente.');
        } catch (\Exception $dbException) {
            DB::rollBack();

            Log::error('✗ ERROR en la base de datos al eliminar la ficha', [
                'ficha_id' => $id,
                'error' => $dbException->getMessage(),
                'file' => $dbException->getFile(),
                'line' => $dbException->getLine(),
                'trace' => $dbException->getTraceAsString(),
            ]);

            throw new \Exception('Error al eliminar la ficha de la base de datos: '.$dbException->getMessage());
        }
    }
}
