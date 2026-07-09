<?php

namespace App\Jobs;

use App\Jobs\Concerns\VerificarPrestamosProximos\HandlesVerificarPrestamosProximosJobExecutionActions;
use App\Jobs\Concerns\VerificarPrestamosProximos\HandlesVerificarPrestamosProximosJobProcessingHelpers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerificarPrestamosProximosJob implements ShouldQueue
{
    use Dispatchable;
    use HandlesVerificarPrestamosProximosJobExecutionActions;
    use HandlesVerificarPrestamosProximosJobProcessingHelpers;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        //
    }
}
