<?php

namespace App\Http\Controllers\Concerns\Caracterizacion;

use App\Models\CaracterizacionPrograma;
use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use App\Models\ProgramaFormacion;
use App\Models\Sede;
use Illuminate\Http\Request;

trait HandlesCaracterizacionCrudReadActions
{
    use HandlesCaracterizacionFormDataHelpers;

    /**
     * Muestra una lista de todos los caracteres con sus fichas asociadas.
     *
     * Este método recupera todos los registros de la tabla `CaracterizacionPrograma`
     * junto con sus relaciones `ficha` y los pasa a la vista `caracterizacion.index`.
     *
     * @return \Illuminate\View\View La vista que muestra la lista de caracteres.
     */
    public function index()
    {
        $caracteres = CaracterizacionPrograma::with('ficha')->orderBy('id', 'desc')->paginate(7);

        return view('caracterizacion.index', compact('caracteres'));
    }

    /**
     * Muestra la vista para crear una nueva caracterización.
     *
     * @return \Illuminate\View\View La vista de creación de caracterización con todas las fichas de caracterización.
     */
    public function create()
    {
        return view('caracterizacion.create', [
            'fichas' => FichaCaracterizacion::all(),
        ]);
    }

    /**
     * Obtiene la caracterización por ficha.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP que contiene el ID de la ficha.
     * @return \Illuminate\View\View La vista de caracterización con los datos de la ficha, sede, instructores y jornadas.
     *
     * @throws \Illuminate\Validation\ValidationException Si la validación del ID de la ficha falla.
     */
    public function getCaracterByFicha(Request $request)
    {
        $request->validate([
            'ficha_id' => 'required|integer|exists:fichas_caracterizacion,id',
        ]);

        $fichaId = $request->input('ficha_id');

        $ficha = FichaCaracterizacion::with(['programaFormacion'])->find($fichaId);
        $sedePrograma = $ficha->programaFormacion->sede_id;

        $sede = Sede::find($sedePrograma);
        $instructors = Instructor::all();
        $jornadas = $this->getCaracterizacionJornadas();

        return view('caracterizacion.caracterizacion', compact('ficha', 'sede', 'instructors', 'jornadas'));
    }

    /**
     * Muestra el formulario de edición para una caracterización específica.
     *
     * @param  string  $id  El ID de la caracterización a editar.
     * @return \Illuminate\View\View La vista del formulario de edición con los datos necesarios.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si no se encuentra la caracterización con el ID proporcionado.
     */
    public function edit(string $id)
    {
        $caracterizacion = CaracterizacionPrograma::findOrFail($id);

        return view('caracterizacion.edit', [
            'caracterizacion' => $caracterizacion,
            'fichas' => FichaCaracterizacion::all(),
            'programas' => ProgramaFormacion::all(),
            'instructores' => Instructor::all(),
            'jornadas' => $this->getCaracterizacionJornadas(),
            'sedes' => Sede::all(),
        ]);
    }

    public function show(Request $request)
    {
        $search = $request->input('search');

        $caracteres = CaracterizacionPrograma::with(['ficha', 'programaFormacion', 'persona'])
            ->whereHas('ficha', function ($query) use ($search) {
                $query->where('ficha', 'like', '%'.$search.'%');
            })
            ->orWhereHas('programaFormacion', function ($query) use ($search) {
                $query->where('nombre', 'like', '%'.$search.'%');
            })
            ->orWhereHas('persona', function ($query) use ($search) {
                $query->where('primer_nombre', 'like', '%'.$search.'%')
                    ->orWhere('segundo_nombre', 'like', '%'.$search.'%')
                    ->orWhere('primer_apellido', 'like', '%'.$search.'%')
                    ->orWhere('segundo_apellido', 'like', '%'.$search.'%');
            })
            ->orderBy('id', 'desc')
            ->paginate(7);

        if ($caracteres->isEmpty()) {
            return redirect()->route('caracterizacion.index')->with('error', 'No se encontraron resultados.');
        }

        return view('caracterizacion.index', compact('caracteres'));
    }
}
