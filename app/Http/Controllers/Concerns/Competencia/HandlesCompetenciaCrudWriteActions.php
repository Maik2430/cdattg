<?php

namespace App\Http\Controllers\Concerns\Competencia;

use App\Http\Requests\StoreCompetenciaRequest;
use App\Models\Competencia;
use App\Models\ProgramaFormacion;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesCompetenciaCrudWriteActions
{
    public function create()
    {
        try {
            $programas = ProgramaFormacion::orderBy('nombre')->get(['id', 'codigo', 'nombre']);

            return view('competencias.create', compact('programas'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de creación de competencia: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al cargar el formulario de creación.');
        }
    }

    public function store(StoreCompetenciaRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $programasSeleccionados = $request->input('programas', []);

            $data['user_create_id'] = Auth::id();
            $data['user_edit_id'] = Auth::id();
            $data['fecha_inicio'] = now();
            $data['fecha_fin'] = now()->addYear();
            $data['status'] = 1;

            $competencia = Competencia::create($data);

            if (! empty($programasSeleccionados)) {
                $competencia->programasFormacion()->attach(
                    collect($programasSeleccionados)->mapWithKeys(function ($programaId) {
                        return [
                            $programaId => [
                                'user_create_id' => Auth::id(),
                                'user_edit_id' => Auth::id(),
                            ],
                        ];
                    })->toArray()
                );
            }

            DB::commit();

            Log::info('Competencia creada exitosamente', [
                'competencia_id' => $competencia->id,
                'codigo' => $competencia->codigo,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('competencias.index')
                ->with('success', "Competencia '{$competencia->codigo}' creada exitosamente.");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear competencia: '.$e->getMessage(), [
                'user_id' => Auth::id(),
                'data' => $request->all(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la competencia. Intente nuevamente.');
        }
    }

    public function edit(Competencia $competencia)
    {
        try {
            $competencia->load([
                'programasFormacion' => function ($query) {
                    $query->orderBy('nombre');
                },
                'resultadosAprendizaje' => function ($query) {
                    $query->orderBy('codigo');
                },
            ]);

            return view('competencias.edit', compact('competencia'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de edición: '.$e->getMessage(), [
                'competencia_id' => $competencia->id,
            ]);

            return redirect()->back()->with('error', 'Error al cargar el formulario de edición.');
        }
    }

    public function destroy(Competencia $competencia): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $cantidadRAPs = $competencia->resultadosCompetencia->count();
            if ($cantidadRAPs > 0) {
                Log::warning('Intento de eliminar competencia con RAPs asociados', [
                    'competencia_id' => $competencia->id,
                    'codigo' => $competencia->codigo,
                    'cantidad_raps' => $cantidadRAPs,
                    'user_id' => Auth::id(),
                ]);

                return redirect()->back()
                    ->with('error', "No se puede eliminar la competencia '{$competencia->codigo}' porque tiene {$cantidadRAPs} resultado(s) de aprendizaje asociado(s). Primero debe desasociar o eliminar los RAPs relacionados.");
            }

            $competencia->delete();

            DB::commit();

            Log::info('Competencia eliminada exitosamente', [
                'competencia_id' => $competencia->id,
                'codigo' => $competencia->codigo,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('competencias.index')
                ->with('success', "Competencia '{$competencia->codigo}' eliminada exitosamente.");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar competencia: '.$e->getMessage(), [
                'competencia_id' => $competencia->id,
                'codigo' => $competencia->codigo ?? 'N/A',
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Error al eliminar la competencia. Intente nuevamente.');
        }
    }
}
