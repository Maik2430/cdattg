<?php

namespace App\Services;

use App\Repositories\TemaRepository;
use App\Services\Concerns\PersonaImport\HandlesPersonaImportDocumentConfigHelpers;
use App\Services\Concerns\PersonaImport\HandlesPersonaImportDocumentoCacheHelpers;
use App\Services\Concerns\PersonaImport\HandlesPersonaImportDuplicateValidationHelpers;
use App\Services\Concerns\PersonaImport\HandlesPersonaImportHeaderHelpers;
use App\Services\Concerns\PersonaImport\HandlesPersonaImportIniciarActions;
use App\Services\Concerns\PersonaImport\HandlesPersonaImportPersistHelpers;
use App\Services\Concerns\PersonaImport\HandlesPersonaImportProcesarActions;
use App\Services\Concerns\PersonaImport\HandlesPersonaImportRecordProcessingHelpers;
use App\Services\Concerns\PersonaImport\HandlesPersonaImportRetryErrorHelpers;
use App\Services\Concerns\PersonaImport\HandlesPersonaImportRowMappingHelpers;
use App\Services\Concerns\PersonaImport\HandlesPersonaImportStateHelpers;

class PersonaImportService
{
    use HandlesPersonaImportDocumentConfigHelpers;
    use HandlesPersonaImportDocumentoCacheHelpers;
    use HandlesPersonaImportDuplicateValidationHelpers;
    use HandlesPersonaImportHeaderHelpers;
    use HandlesPersonaImportIniciarActions;
    use HandlesPersonaImportPersistHelpers;
    use HandlesPersonaImportProcesarActions;
    use HandlesPersonaImportRecordProcessingHelpers;
    use HandlesPersonaImportRetryErrorHelpers;
    use HandlesPersonaImportRowMappingHelpers;
    use HandlesPersonaImportStateHelpers;

    public function __construct(PersonaService $personaService, TemaRepository $temaRepository)
    {
        $this->personaService = $personaService;
        $this->temaRepository = $temaRepository;
    }
}
