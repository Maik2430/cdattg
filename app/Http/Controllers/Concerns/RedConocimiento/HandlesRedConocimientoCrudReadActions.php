<?php

namespace App\Http\Controllers\Concerns\RedConocimiento;

use App\Models\RedConocimiento;
use App\Models\Regional;
use Illuminate\Support\Facades\Log;

trait HandlesRedConocimientoCrudReadActions
{
    /**
     * Muestra el listado de redes de conocimiento.
     */
    public function index()
    {
        try {
            // Ya no necesitamos pasar datos, Livewire se encarga
            return view('red_conocimiento.index');
        } catch (\Exception $e) {
            Log::error('Error al cargar vista de redes de conocimiento: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al cargar redes de conocimiento.');
        }
    }

    /**
     * Muestra el formulario para crear una nueva red de conocimiento.
     */
    public function create()
    {
        try {
            $regionales = Regional::where('status', 1)->get();

            return view('red_conocimiento.create', compact('regionales'));
        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de creación: '.$e->getMessage());

            return redirect()->route('red-conocimiento.index')
                ->withErrors(['error' => 'Ocurrió un error al cargar el formulario.']);
        }
    }

    /**
     * Muestra los detalles de una red de conocimiento específica.
     */
    public function show(RedConocimiento $redConocimiento)
    {
        try {
            $redConocimiento->load('regional');

            return view('red_conocimiento.show', compact('redConocimiento'));
        } catch (\Exception $e) {
            Log::error('Error al mostrar red de conocimiento: '.$e->getMessage());

            return redirect()->route('red-conocimiento.index')
                ->withErrors(['error' => 'Ocurrió un error al cargar los detalles.']);
        }
    }

    /**
     * Muestra el formulario para editar una red de conocimiento.
     */
    public function edit(RedConocimiento $redConocimiento)
    {
        try {
            $regionales = Regional::where('status', 1)->get();

            return view('red_conocimiento.edit', compact('redConocimiento', 'regionales'));
        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de edición: '.$e->getMessage());

            return redirect()->route('red-conocimiento.index')
                ->withErrors(['error' => 'Ocurrió un error al cargar el formulario de edición.']);
        }
    }
}
