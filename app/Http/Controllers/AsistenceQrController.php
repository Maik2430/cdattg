<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\QrAsistence\HandlesQrAsistenceActivityActions;
use App\Http\Controllers\Concerns\QrAsistence\HandlesQrAsistenceBulkRegistration;
use App\Http\Controllers\Concerns\QrAsistence\HandlesQrAsistenceCaracterSelection;
use App\Http\Controllers\Concerns\QrAsistence\HandlesQrAsistenceEvidenciaActions;
use App\Http\Controllers\Concerns\QrAsistence\HandlesQrAsistenceFinalizeActions;
use App\Http\Controllers\Concerns\QrAsistence\HandlesQrAsistenceReadActions;
use App\Http\Controllers\Concerns\QrAsistence\HandlesQrAsistenceScheduleActions;
use App\Http\Controllers\Concerns\QrAsistence\HandlesQrAsistenceStoreActions;
use App\Http\Controllers\Concerns\QrAsistence\HandlesQrAsistenceVerifyDocument;
use App\Http\Controllers\Concerns\QrAsistence\HandlesQrAsistenceWebFormActions;
use App\Http\Controllers\Concerns\QrAsistence\HandlesQrAsistenceWebListActions;
use App\Services\AsistenceQrService;
use App\Services\RegistroActividadesServices;

class AsistenceQrController extends Controller
{
    use HandlesQrAsistenceActivityActions;
    use HandlesQrAsistenceBulkRegistration;
    use HandlesQrAsistenceCaracterSelection;
    use HandlesQrAsistenceEvidenciaActions;
    use HandlesQrAsistenceFinalizeActions;
    use HandlesQrAsistenceReadActions;
    use HandlesQrAsistenceScheduleActions;
    use HandlesQrAsistenceStoreActions;
    use HandlesQrAsistenceVerifyDocument;
    use HandlesQrAsistenceWebFormActions;
    use HandlesQrAsistenceWebListActions;

    protected AsistenceQrService $asistenceQrService;

    protected RegistroActividadesServices $registroActividadesService;

    public function __construct(AsistenceQrService $asistenceQrService, RegistroActividadesServices $registroActividadesService)
    {
        $this->asistenceQrService = $asistenceQrService;
        $this->registroActividadesService = $registroActividadesService;
        $this->middleware('auth');
    }
}
