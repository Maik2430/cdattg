<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Concerns\Complementarios\DocumentoComplementario\HandlesDocumentoComplementarioCrudReadActions;
use App\Http\Controllers\Concerns\Complementarios\DocumentoComplementario\HandlesDocumentoComplementarioCrudWriteActions;
use App\Http\Controllers\Controller;
use App\Services\Complementarios\ComplementarioService;

class DocumentoComplementarioController extends Controller
{
    use HandlesDocumentoComplementarioCrudReadActions;
    use HandlesDocumentoComplementarioCrudWriteActions;

    public function __construct(
        private readonly ComplementarioService $complementarioService
    ) {}
}
