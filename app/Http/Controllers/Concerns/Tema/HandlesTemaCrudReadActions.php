<?php

namespace App\Http\Controllers\Concerns\Tema;

use App\Models\Parametro;
use App\Models\Tema;

trait HandlesTemaCrudReadActions
{
    public function index()
    {
        $temas = $this->temaService->listar(10);

        return view('temas.index', compact('temas'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Tema $tema)
    {
        return view('temas.show', compact('tema'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tema $tema)
    {
        $parametros = parametro::where('status', 1)->get();

        return view('temas.edit', compact('tema', 'parametros'));
    }
}
