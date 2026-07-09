<?php

namespace App\Http\Controllers\Concerns\Competencia;

use App\Models\Competencia;
use App\Models\ResultadosAprendizaje;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesCompetenciaResultadosActions
{
    use HandlesCompetenciaResultadosAssociationHelpers;

    public function gestionarResultados(Competencia $competencia)
    {
        try {
            return view('competencias.gestionar-resultados', compact('competencia'));
        } catch (Exception $e) {
            Log::error('Error al gestionar resultados de competencia: '.$e->getMessage(), [
                'competencia_id' => $competencia->id,
            ]);

            return redirect()->back()->with('error', 'Error al cargar la gestión de resultados.');
        }
    }

    public function asociarResultado(Request $request, Competencia $competencia): RedirectResponse
    {
        try {
            $request->validate([
                'resultado_id' => 'required|exists:resultados_aprendizajes,id',
            ]);

            DB::beginTransaction();

            $resultadoId = (int) $request->resultado_id;
            $validacion = $this->validateResultadoForAssociation($competencia, $resultadoId);

            if ($validacion['error'] !== null) {
                return redirect()->back()->with('error', $validacion['error']);
            }

            $resultado = $this->attachResultadoToCompetencia($competencia, $resultadoId);

            DB::commit();

            Log::info('Resultado de aprendizaje asociado a competencia', [
                'competencia_id' => $competencia->id,
                'resultado_id' => $resultadoId,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('success', "Resultado de aprendizaje '{$resultado->codigo}' asociado exitosamente.");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al asociar resultado: '.$e->getMessage(), [
                'competencia_id' => $competencia->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Error al asociar el resultado de aprendizaje.');
        }
    }

    public function asociarResultados(Request $request, Competencia $competencia): RedirectResponse
    {
        try {
            $request->validate([
                'resultado_ids' => 'required|array|min:1',
                'resultado_ids.*' => 'exists:resultados_aprendizajes,id',
            ]);

            DB::beginTransaction();

            if (! $competencia->status) {
                return redirect()->back()->with('error', 'No se pueden asociar resultados a una competencia inactiva.');
            }

            $asociadosExitosamente = [];
            $errores = [];

            foreach ($request->resultado_ids as $resultadoId) {
                try {
                    $resultado = ResultadosAprendizaje::findOrFail($resultadoId);

                    if (! $resultado->status) {
                        $errores[] = "El resultado '{$resultado->codigo}' está inactivo y no se puede asociar.";

                        continue;
                    }

                    if ($competencia->resultadosAprendizaje()->where('resultados_aprendizajes.id', $resultadoId)->exists()) {
                        $errores[] = "El resultado '{$resultado->codigo}' ya está asignado a la competencia.";

                        continue;
                    }

                    $resultado = $this->attachResultadoToCompetencia($competencia, (int) $resultadoId, updateResultadoModel: true);
                    $asociadosExitosamente[] = $resultado->codigo;

                    Log::info('Resultado de aprendizaje asociado a competencia (múltiple)', [
                        'competencia_id' => $competencia->id,
                        'resultado_id' => $resultadoId,
                        'resultado_codigo' => $resultado->codigo,
                        'user_id' => Auth::id(),
                    ]);
                } catch (Exception $e) {
                    $errores[] = "Error al asociar resultado ID {$resultadoId}: ".$e->getMessage();
                }
            }

            if (! empty($asociadosExitosamente)) {
                $this->redistribuirDuracionResultados($competencia);
            }

            DB::commit();

            $mensaje = '';
            if (! empty($asociadosExitosamente)) {
                $mensaje .= 'Resultados asociados exitosamente: '.implode(', ', $asociadosExitosamente);
            }

            if (! empty($errores)) {
                if (! empty($mensaje)) {
                    $mensaje .= "\n\nErrores encontrados:\n".implode("\n", $errores);
                } else {
                    $mensaje = "No se pudieron asociar los resultados:\n".implode("\n", $errores);
                }

                return redirect()->back()->with('warning', $mensaje);
            }

            return redirect()->back()->with('success', $mensaje);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al asociar múltiples resultados: '.$e->getMessage(), [
                'competencia_id' => $competencia->id,
                'resultado_ids' => $request->resultado_ids ?? [],
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Error al asociar los resultados de aprendizaje.');
        }
    }

    public function desasociarResultado(Competencia $competencia, ResultadosAprendizaje $resultado): RedirectResponse
    {
        try {
            DB::beginTransaction();

            if (! $competencia->resultadosAprendizaje()->where('resultados_aprendizajes.id', $resultado->id)->exists()) {
                return redirect()->back()->with('error', 'Este resultado de aprendizaje no está asignado a la competencia.');
            }

            $competencia->resultadosAprendizaje()->detach($resultado->id);
            $this->redistribuirDuracionResultados($competencia);

            DB::commit();

            return redirect()->back()->with('success', 'Resultado de aprendizaje desasociado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al desasociar resultado: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al desasociar el resultado de aprendizaje.');
        }
    }
}
