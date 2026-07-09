<?php

namespace App\Http\Controllers\Concerns\Complementarios\DocumentoComplementario;

use App\Models\Complementarios\ComplementarioOfertado;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

trait HandlesDocumentoComplementarioCrudReadActions
{
    /**
     * Mostrar formulario para subir documentos
     */
    public function formularioDocumentos(Request $request, $id): Factory|View
    {
        $programa = ComplementarioOfertado::findOrFail($id);

        // Obtener aspirante_id de la URL
        $aspirante_id = $request->query('aspirante_id');

        return view('complementarios.inscripciones.documents', compact('programa', 'aspirante_id'));
    }

    /**
     * Procesar documentos (método legacy)
     */
    public function procesarDocumentos(): Factory|View
    {
        $tiposDocumento = $this->complementarioService->getTiposDocumento();

        return view('complementarios.inscripciones.processing', compact('tiposDocumento'));
    }
}
