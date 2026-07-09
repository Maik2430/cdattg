<?php

namespace App\Services\Complementarios\Sofia;

use App\Services\Concerns\Complementarios\SofiaValidationProcessor\HandlesSofiaValidationProcessorBatchActions;
use App\Services\Concerns\Complementarios\SofiaValidationProcessor\HandlesSofiaValidationProcessorStatsHelpers;
use App\Services\Concerns\Complementarios\SofiaValidationProcessor\HandlesSofiaValidationProcessorTimingHelpers;

class SofiaValidationProcessor
{
    use HandlesSofiaValidationProcessorBatchActions;
    use HandlesSofiaValidationProcessorStatsHelpers;
    use HandlesSofiaValidationProcessorTimingHelpers;

    private const BATCH_SIZE = 5;

    private const BATCH_DELAY_SECONDS = 3;

    private const DELAY_INITIAL_MS = 3000;

    private const DELAY_MID_MS = 2000;

    private const DELAY_FINAL_MS = 1000;

    private const PROGRESS_THRESHOLD_LOW = 0.2;

    private const PROGRESS_THRESHOLD_MID = 0.5;

    private SofiaValidationService $validationService;

    private int $batchSize;

    private int $batchDelay;

    public function __construct(SofiaValidationService $validationService)
    {
        $this->validationService = $validationService;
        $this->batchSize = self::BATCH_SIZE;
        $this->batchDelay = self::BATCH_DELAY_SECONDS;
    }
}
