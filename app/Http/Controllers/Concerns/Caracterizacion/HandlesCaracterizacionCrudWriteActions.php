<?php

namespace App\Http\Controllers\Concerns\Caracterizacion;

use App\Models\AsistenciaAprendiz;
use App\Models\CaracterizacionPrograma;
use Illuminate\Http\Request;

trait HandlesCaracterizacionCrudWriteActions
{
    /**
     * Almacena una nueva caracterización en la base de datos.
     *
     * Este método valida los datos de entrada y crea una nueva instancia de
     * CaracterizacionPrograma con los datos proporcionados. Luego guarda la
     * instancia en la base de datos y redirige al usuario al índice de
     * caracterizaciones con un mensaje de éxito.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP que contiene los datos de entrada.
     * @return \Illuminate\Http\RedirectResponse Redirección a la ruta de índice de caracterizaciones con un mensaje de éxito.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ficha_id' => 'required|exists:fichas_caracterizacion,id',
            'programa_id' => 'required|exists:programas_formacion,id',
            'sede_id' => 'required|exists:sedes,id',
            'jornada_id' => 'required|exists:parametros_temas,id',
            'persona_id' => 'required|exists:instructors,persona_id',
        ]);

        $caracterizacion = new CaracterizacionPrograma;
        $caracterizacion->ficha_id = $request->input('ficha_id');
        $caracterizacion->programa_formacion_id = $request->input('programa_id');
        $caracterizacion->instructor_persona_id = $request->input('persona_id');
        $caracterizacion->jornada_id = $request->input('jornada_id');
        $caracterizacion->sede_id = $request->input('sede_id');

        $caracterizacion->save();

        return redirect()->route('caracterizacion.index')->with('success', 'Caracterización creada exitosamente.');
    }

    /**
     * Actualiza una caracterización existente en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP que contiene los datos de la caracterización a actualizar.
     * @param  string  $id  El ID de la caracterización que se va a actualizar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta de índice de caracterización con un mensaje de éxito.
     *
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos de la solicitud falla.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si no se encuentra la caracterización con el ID proporcionado.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'ficha_id' => 'required|exists:fichas_caracterizacion,id',
            'programa_formacion_id' => 'required|exists:programas_formacion,id',
            'instructor_persona_id' => 'required|exists:instructors,persona_id',
            'jornada_id' => 'required|exists:parametros_temas,id',
            'sede_id' => 'required|exists:sedes,id',
        ]);

        $caracterizacion = CaracterizacionPrograma::findOrFail($id);
        $caracterizacion->ficha_id = $request->input('ficha_id');
        $caracterizacion->programa_formacion_id = $request->input('programa_formacion_id');
        $caracterizacion->instructor_persona_id = $request->input('instructor_persona_id');
        $caracterizacion->jornada_id = $request->input('jornada_id');
        $caracterizacion->sede_id = $request->input('sede_id');

        $caracterizacion->save();

        return redirect()->route('caracterizacion.index')->with('success', 'Caracterización actualizada exitosamente.');
    }

    /**
     * Elimina una caracterización específica basada en el ID proporcionado.
     *
     * @param  string  $id  El ID de la caracterización a eliminar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta de índice de caracterización con un mensaje de éxito.
     */
    public function destroy(string $id)
    {
        $caracterizacion = CaracterizacionPrograma::where('id', $id);

        $asistencias = AsistenciaAprendiz::where('caracterizacion_id', $id)->get();

        if (! empty($asistencias)) {
            return redirect()->route('caracterizacion.index')->with('error', 'No se puede eliminar la caracterización porque tiene asistencias asociadas.');
        }

        $caracterizacion->delete();

        return redirect()->route('caracterizacion.index')->with('success', 'Caracterización eliminada exitosamente.');
    }
}
