<?php

namespace App\Livewire;

use App\Livewire\Concerns\PersonaImport\HandlesPersonaImportDeleteActions;
use App\Livewire\Concerns\PersonaImport\HandlesPersonaImportDeleteTransactionHelpers;
use App\Livewire\Concerns\PersonaImport\HandlesPersonaImportFileDeleteHelpers;
use App\Livewire\Concerns\PersonaImport\HandlesPersonaImportHistoryActions;
use App\Livewire\Concerns\PersonaImport\HandlesPersonaImportIssueTranslationHelpers;
use App\Livewire\Concerns\PersonaImport\HandlesPersonaImportMountHelpers;
use App\Livewire\Concerns\PersonaImport\HandlesPersonaImportProgressActions;
use App\Livewire\Concerns\PersonaImport\HandlesPersonaImportRenderActions;
use App\Livewire\Concerns\PersonaImport\HandlesPersonaImportSelectionActions;
use App\Livewire\Concerns\PersonaImport\HandlesPersonaImportStopActions;
use App\Livewire\Concerns\PersonaImport\HandlesPersonaImportUploadActions;
use Livewire\Component;
use Livewire\WithFileUploads;

class PersonaImportComponent extends Component
{
    use HandlesPersonaImportDeleteActions;
    use HandlesPersonaImportDeleteTransactionHelpers;
    use HandlesPersonaImportFileDeleteHelpers;
    use HandlesPersonaImportHistoryActions;
    use HandlesPersonaImportIssueTranslationHelpers;
    use HandlesPersonaImportMountHelpers;
    use HandlesPersonaImportProgressActions;
    use HandlesPersonaImportRenderActions;
    use HandlesPersonaImportSelectionActions;
    use HandlesPersonaImportStopActions;
    use HandlesPersonaImportUploadActions;
    use WithFileUploads;

    private const ARCHIVO_POR_DEFECTO = 'Ningún archivo';

    private const PATRON_PAYLOAD_IMPORT_ID = '%"importId";i:';

    public $archivo;

    public $archivoNombre = self::ARCHIVO_POR_DEFECTO;

    public $mostrarProgreso = false;

    public $importacionId = null;

    public $importacionSeleccionada = null;

    public $importaciones = [];

    public $importacionesActivas = [];

    public $plantillaDisponible = false;

    public $procesados = 0;

    public $total = 0;

    public $exitosos = 0;

    public $duplicados = 0;

    public $faltantes = 0;

    public $estado = 'PENDIENTE';

    public $estadoColor = 'secondary';

    public $issues = [];

    protected $listeners = ['actualizarProgreso', 'recargarHistorial'];
}
