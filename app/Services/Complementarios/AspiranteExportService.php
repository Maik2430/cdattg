<?php

namespace App\Services\Complementarios;

use App\Repositories\Complementarios\AspiranteComplementarioRepository;
use App\Repositories\Complementarios\ComplementarioOfertadoRepository;
use App\Services\Concerns\Complementarios\AspiranteExport\HandlesAspiranteExportDocumentHelpers;
use App\Services\Concerns\Complementarios\AspiranteExport\HandlesAspiranteExportExcelActions;
use App\Services\Concerns\Complementarios\AspiranteExport\HandlesAspiranteExportSpreadsheetHelpers;

class AspiranteExportService
{
    use HandlesAspiranteExportDocumentHelpers;
    use HandlesAspiranteExportExcelActions;
    use HandlesAspiranteExportSpreadsheetHelpers;

    private const COLOR_NEGRO_RGB = '000000';

    public function __construct(
        private readonly AspiranteComplementarioRepository $aspiranteRepository,
        private readonly ComplementarioOfertadoRepository $programaRepository,
        private readonly AspiranteComplementarioService $aspiranteComplementarioService,
        private readonly AspiranteDocumentoService $documentoService
    ) {}
}
