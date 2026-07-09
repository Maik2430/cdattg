<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreevidenciasRequest;
use App\Http\Requests\UpdateevidenciasRequest;
use App\Models\evidencias;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\ResultadosAprendizaje;
use App\Services\RegistroActividadesServices;

class EvidenciasController extends Controller
{
    public function __construct(
        private readonly RegistroActividadesServices $registroActividadesServices
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(InstructorFichaCaracterizacion $caracterizacion)
    {
        try {
            $actividades = $this->registroActividadesServices->getActividades($caracterizacion);
            $rapActual = $caracterizacion->resultadosAprendizaje->first();

            return view('registro_actividades.create', compact('caracterizacion', 'rapActual', 'actividades'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreevidenciasRequest $request, $caracterizacion)
    {
        try {
            $data = $request->validated();

            // Crear la evidencia
            $evidencia = Evidencias::create([
                'codigo' => 'EVID-'.time(), // Generar código único
                'nombre' => $data['name'],
                'user_create_id' => auth()->id(),
                'user_edit_id' => auth()->id(),
            ]);

            // Obtener el resultado de aprendizaje seleccionado
            $resultadoAprendizaje = ResultadosAprendizaje::find($data['resultado_aprendizaje_id']);

            // Aquí podrías agregar lógica adicional para relacionar la evidencia con la guía de aprendizaje
            // basada en el resultado de aprendizaje seleccionado

            return redirect()->back()->with('success', 'Actividad registrada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error al registrar la actividad: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(evidencias $evidencias)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(evidencias $evidencias)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateevidenciasRequest $request, evidencias $evidencias)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(evidencias $evidencias)
    {
        //
    }
}
