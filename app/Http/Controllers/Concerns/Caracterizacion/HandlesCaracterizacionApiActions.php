<?php

namespace App\Http\Controllers\Concerns\Caracterizacion;

use App\Models\CaracterizacionPrograma;

trait HandlesCaracterizacionApiActions
{
    /**
     * Obtiene las caracterizaciones asociadas a un instructor específico.
     *
     * @param  string  $id  El ID del instructor.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con las caracterizaciones encontradas o un mensaje de error si no se encuentran.
     *
     * Este método realiza una consulta a la base de datos para obtener las caracterizaciones que están asociadas al instructor cuyo ID se proporciona como parámetro.
     * Utiliza relaciones Eloquent para incluir datos de las tablas relacionadas: ficha, programa de formación, persona, jornada y sede.
     * Luego, mapea los resultados para devolver solo los campos necesarios en la respuesta JSON.
     * Si se encuentran caracterizaciones, se devuelve una respuesta JSON con un código de estado 200.
     * Si no se encuentran caracterizaciones, se devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
     */
    public function CaracterizacionByInstructor(string $id)
    {
        $caracterizaciones = CaracterizacionPrograma::with('ficha', 'programaFormacion', 'persona', 'jornada', 'sede')
            ->where('instructor_persona_id', $id)
            ->get()
            ->map(function ($caracterizacion) {
                $persona = $caracterizacion->persona;
                $personaNombre = 'N/A';
                if ($persona) {
                    $personaNombre = $persona->nombre_completo;
                    if ($personaNombre === '') {
                        $personaNombre = trim(implode(' ', array_filter([
                            $persona->primer_nombre,
                            $persona->segundo_nombre,
                            $persona->primer_apellido,
                            $persona->segundo_apellido,
                        ]))) ?: 'N/A';
                    }
                }

                return [
                    'id' => $caracterizacion->id,
                    'ficha' => $caracterizacion->ficha->ficha ?? 'N/A',
                    'programa_formacion' => $caracterizacion->programaFormacion->nombre ?? 'N/A',
                    'persona' => $personaNombre,
                    'jornada' => $caracterizacion->jornada->jornada ?? 'N/A',
                    'sede' => $caracterizacion->sede->sede ?? 'N/A',
                ];
            });

        if ($caracterizaciones->isNotEmpty()) {
            return response()->json($caracterizaciones, 200);
        } else {
            return response()->json(['message' => 'No se encontraron caracterizaciones.'], 404);
        }
    }
}
