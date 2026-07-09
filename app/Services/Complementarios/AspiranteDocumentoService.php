<?php

namespace App\Services\Complementarios;

use App\Services\Concerns\Complementarios\AspiranteDocumento\HandlesAspiranteDocumentoDriveActions;
use App\Services\Concerns\Complementarios\AspiranteDocumento\HandlesAspiranteDocumentoFileMatchHelpers;
use App\Services\Concerns\Complementarios\AspiranteDocumento\HandlesAspiranteDocumentoPatronHelpers;
use App\Services\Concerns\Complementarios\AspiranteDocumento\HandlesAspiranteDocumentoPdfHelpers;
use App\Services\Concerns\Complementarios\AspiranteDocumento\HandlesAspiranteDocumentoSearchActions;
use App\Services\Concerns\Complementarios\AspiranteDocumento\HandlesAspiranteDocumentoUploadActions;

class AspiranteDocumentoService
{
    use HandlesAspiranteDocumentoDriveActions;
    use HandlesAspiranteDocumentoFileMatchHelpers;
    use HandlesAspiranteDocumentoPatronHelpers;
    use HandlesAspiranteDocumentoPdfHelpers;
    use HandlesAspiranteDocumentoSearchActions;
    use HandlesAspiranteDocumentoUploadActions;
}
