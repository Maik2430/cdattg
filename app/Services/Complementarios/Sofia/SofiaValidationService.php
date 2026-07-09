<?php

namespace App\Services\Complementarios\Sofia;

use App\Services\AuditoriaService;
use App\Services\Concerns\Complementarios\SofiaValidation\HandlesSofiaValidationAuditHelpers;
use App\Services\Concerns\Complementarios\SofiaValidation\HandlesSofiaValidationValidateActions;

class SofiaValidationService
{
    use HandlesSofiaValidationAuditHelpers;
    use HandlesSofiaValidationValidateActions;

    private SofiaHttpClient $httpClient;

    private SofiaStateMapper $stateMapper;

    private AuditoriaService $auditoriaService;

    public function __construct(
        SofiaHttpClient $httpClient,
        SofiaStateMapper $stateMapper,
        AuditoriaService $auditoriaService
    ) {
        $this->httpClient = $httpClient;
        $this->stateMapper = $stateMapper;
        $this->auditoriaService = $auditoriaService;
    }
}
