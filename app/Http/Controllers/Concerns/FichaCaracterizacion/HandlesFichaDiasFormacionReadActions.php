<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesFichaDiasFormacionReadActions
{
    /**
     * Muestra la vista para gestionar días de formación de una ficha.
     *
     * @param  int  $id  El ID de la ficha.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function gestionarDiasFormacion(string $id)
    {
        // try {
        Log::info('Acceso a gestión de días de formación', [
            'user_id' => Auth::id(),
            'ficha_id' => $id,
            'timestamp' => now(),
        ]);

        // Buscar la ficha con sus relaciones
        $ficha = FichaCaracterizacion::with([
            'programaFormacion',
            'jornadaFormacion.parametro',
            'diasFormacion.dia',
            'sede',
        ])->findOrFail($id);

        // Obtener días de la semana disponibles
        $diasSemana = \App\Models\Parametro::whereIn('id', [12, 13, 14, 15, 16, 17]) // LUNES a SÁBADO
            ->orderBy('id')
            ->get();

        // Obtener días ya asignados a esta ficha
        $diasAsignados = $ficha->diasFormacion()
            ->with('dia')
            ->get();

        // Configuración de jornadas y días permitidos
        $configuracionJornadas = $this->obtenerConfiguracionJornadas();

        // Calcular horas totales actuales
        $horasTotalesActuales = $this->calcularHorasTotales($diasAsignados, $ficha);

        Log::info('Datos de gestión de días cargados', [
            'ficha_id' => $id,
            'total_dias_disponibles' => $diasSemana->count(),
            'dias_asignados' => $diasAsignados->count(),
            'horas_totales_actuales' => $horasTotalesActuales,
        ]);

        return view('fichas.gestionar-dias-formacion', compact(
            'ficha',
            'diasSemana',
            'diasAsignados',
            'configuracionJornadas',
            'horasTotalesActuales'
        ));

        // } catch (\Exception $e) {
        //     Log::error('Error al cargar gestión de días de formación', [
        //         'ficha_id' => $id,
        //         'user_id' => Auth::id(),
        //         'error' => $e->getMessage(),
        //         'line' => $e->getLine()
        //     ]);

        //     return redirect()->route('fichaCaracterizacion.index')
        //         ->with('error', 'Error al cargar la gestión de días de formación: ' . $e->getMessage());
        // }
    }
}
