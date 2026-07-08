<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use App\Models\RedConocimiento;
use App\Models\Regional;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorReadActions
{
    public function index(Request $request)
    {
        try {
            $filtros = [
                'search' => $request->input('search'),
                'estado' => $request->input('estado', 'todos'),
                'especialidad' => $request->input('especialidad'),
                'regional' => $request->input('regional'),
                'per_page' => 15,
            ];

            $instructores = $this->instructorService->listarConFiltros($filtros);

            $regionales = Regional::where('status', true)->orderBy('nombre')->get();
            $especialidades = RedConocimiento::where('status', true)->orderBy('nombre')->get();

            $estadisticas = $this->instructorService->obtenerEstadisticas();

            return view(
                'Instructores.index',
                array_merge(
                    compact(
                        'instructores',
                        'regionales',
                        'especialidades',
                        'estadisticas',
                        'filtros'
                    ),
                    $this->loadIndexFormCatalogs()
                )
            );
        } catch (Exception $e) {
            Log::error('Error al listar instructores: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Error al cargar la lista de instructores.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Instructor $instructor)
    {
        try {
            // Cargar relaciones necesarias
            $instructor->load(['jornadas.parametro', 'modalidades.parametro', 'persona']);

            $fichasCaracterizacion = FichaCaracterizacion::all();
            $instructor->persona->edad = Carbon::parse($instructor->persona->fecha_de_nacimiento)->age;
            $instructor->persona->fecha_de_nacimiento = Carbon::parse(
                $instructor->persona->fecha_de_nacimiento
            )->format('d/m/Y');

            return view('Instructores.show', compact('instructor', 'fichasCaracterizacion'));
        } catch (Exception $e) {
            Log::error('Error al mostrar instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('instructor.index')
                ->with('error', 'Error al cargar los datos del instructor. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Instructores.create');
    }
}
