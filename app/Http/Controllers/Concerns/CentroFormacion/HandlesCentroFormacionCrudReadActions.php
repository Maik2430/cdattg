<?php

namespace App\Http\Controllers\Concerns\CentroFormacion;

use App\Models\CentroFormacion;
use App\Models\Regional;

trait HandlesCentroFormacionCrudReadActions
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $centros = CentroFormacion::with('regional')->paginate(10);
        $regionales = Regional::where('status', 1)->get();

        return view('centros.index', compact('centros', 'regionales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regionales = Regional::where('status', 1)->get();

        return view('centros.create', compact('regionales'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $centro = CentroFormacion::with('regional')->findOrFail($id);

        return view('centros.show', compact('centro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $centro = CentroFormacion::with('regional')->findOrFail($id);
        $regionales = Regional::where('status', 1)->get();

        return view('centros.edit', compact('centro', 'regionales'));
    }
}
