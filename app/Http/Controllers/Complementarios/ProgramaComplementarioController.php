<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Complementarios\Concerns\HandlesProgramaComplementarioFormatting;
use App\Http\Controllers\Complementarios\Concerns\HandlesProgramaComplementarioReadActions;
use App\Http\Controllers\Complementarios\Concerns\HandlesProgramaComplementarioWriteActions;
use App\Http\Controllers\Controller;
use App\Services\Complementarios\ComplementarioService;

class ProgramaComplementarioController extends Controller
{
    use HandlesProgramaComplementarioFormatting;
    use HandlesProgramaComplementarioReadActions;
    use HandlesProgramaComplementarioWriteActions;

    public function __construct(
        private readonly ComplementarioService $complementarioService
    ) {}
}
