<?php

namespace App\Http\Controllers\Concerns\Auth;

use Illuminate\View\View;

trait HandlesRegisterCrudReadActions
{
    use HandlesRegisterFormDataHelpers;

    public function create(): View
    {
        return view('user.registro', [
            'documentos' => $this->resolveDocumentos(),
            'generos' => $this->resolveGeneros(),
            'caracterizaciones' => $this->resolveCaracterizaciones(),
            'vias' => $this->resolveVias(),
            'cardinales' => $this->resolveCardinales(),
            'letras' => $this->resolveLetras(),

            'paises' => collect(),
            'departamentos' => collect(),
            'municipios' => collect(),
        ]);
    }
}
