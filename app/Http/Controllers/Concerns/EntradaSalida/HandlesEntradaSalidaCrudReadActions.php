<?php

namespace App\Http\Controllers\Concerns\EntradaSalida;

use App\Models\Ambiente;
use App\Models\EntradaSalida;
use App\Models\FichaCaracterizacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait HandlesEntradaSalidaCrudReadActions
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $fichaCaracterizacion)
    {
        $ficha = FichaCaracterizacion::where('id', $fichaCaracterizacion);

        $registros = EntradaSalida::where('instructor_user_id', Auth::user()->id)
            ->where('fecha', Carbon::now()->toDateString())
            ->where('listado', null)->get();

        // Pasa los registros a la vista
        return view('entradaSalidas.index', compact('registros', 'ficha'));
    }

    public function registros(Request $request)
    {
        $fichaCaracterizacion = $request->ficha_id;
        $ambiente_id = $request->ambiente_id;

        $ambiente = Ambiente::where('id', $ambiente_id)->first();
        $descripcion = $request->descripcion;
        $fecha = Carbon::now()->toDateString();
        $ficha = FichaCaracterizacion::where('id', $fichaCaracterizacion)->first();
        // Obtén todos los registros de entrada/salida del usuario actual
        $registros = EntradaSalida::where('instructor_user_id', Auth::user()->id)
            ->where('fecha', Carbon::now()->toDateString())
            ->where('ficha_caracterizacion_id', $fichaCaracterizacion)
            ->where('listado', null)->get();

        return view('entradaSalidas.index', compact('registros', 'ficha', 'fecha', 'ambiente', 'descripcion'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('entradaSalidas.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(EntradaSalida $entradaSalida)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntradaSalida $entradaSalida)
    {
        return view('entradaSalidas.edit');
    }

    public function cargarDatos(Request $request)
    {
        $request->validate([
            'evento' => 'required',
            'ficha_id',
        ]);
        $ficha_id = $request->ficha_id;
        $evento = $request->evento;
        $ambiente_id = $request->ambiente_id;
        $descripcion = $request->descripcion;
        if ($request->evento == 1) {
            return view('entradaSalidas.create', compact('ficha_id', 'evento', 'ambiente_id', 'descripcion'));
        } else {
            return view('entradaSalidas.edit', compact('ficha_id', 'evento', 'ambiente_id', 'descripcion'));
        }
    }
}
