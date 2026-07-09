<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\EntradaSalida\HandlesEntradaSalidaApiActions;
use App\Http\Controllers\Concerns\EntradaSalida\HandlesEntradaSalidaCrudReadActions;
use App\Http\Controllers\Concerns\EntradaSalida\HandlesEntradaSalidaCrudWriteActions;
use App\Http\Controllers\Concerns\EntradaSalida\HandlesEntradaSalidaExportActions;
use App\Http\Controllers\Concerns\EntradaSalida\HandlesEntradaSalidaSalidaUpdateActions;
use App\Services\EntradaSalidaService;
use App\Services\ExportService;

class EntradaSalidaController extends Controller
{
    use HandlesEntradaSalidaApiActions;
    use HandlesEntradaSalidaCrudReadActions;
    use HandlesEntradaSalidaCrudWriteActions;
    use HandlesEntradaSalidaExportActions;
    use HandlesEntradaSalidaSalidaUpdateActions;

    protected EntradaSalidaService $entradaSalidaService;

    protected ExportService $exportService;

    public function __construct(
        EntradaSalidaService $entradaSalidaService,
        ExportService $exportService
    ) {
        $this->entradaSalidaService = $entradaSalidaService;
        $this->exportService = $exportService;
    }
}
