<?php

namespace App\Jobs;

use App\Jobs\Concerns\ValidarDocumento\HandlesValidarDocumentoJobDriveHelpers;
use App\Jobs\Concerns\ValidarDocumento\HandlesValidarDocumentoJobExecutionActions;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ValidarDocumentoJob implements ShouldQueue
{
    use HandlesValidarDocumentoJobDriveHelpers;
    use HandlesValidarDocumentoJobExecutionActions;
    use Queueable;

    protected $complementarioId;

    protected $userId;

    protected $progressId;

    public function __construct($complementarioId, $userId = null, $progressId = null)
    {
        $this->complementarioId = $complementarioId;
        $this->userId = $userId;
        $this->progressId = $progressId;
    }
}
