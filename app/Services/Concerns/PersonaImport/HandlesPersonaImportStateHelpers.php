<?php

namespace App\Services\Concerns\PersonaImport;

use App\Repositories\TemaRepository;
use App\Services\PersonaService;

trait HandlesPersonaImportStateHelpers
{
    private PersonaService $personaService;

    private TemaRepository $temaRepository;

    private array $headerMap = [];

    private array $documentSeen = [];

    private bool $documentoCacheInitialized = false;

    private array $documentoCache = [];
}
