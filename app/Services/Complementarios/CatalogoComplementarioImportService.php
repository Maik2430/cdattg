<?php

namespace App\Services\Complementarios;

use App\Services\Concerns\Complementarios\CatalogoComplementarioImport\HandlesCatalogoComplementarioImportActions;
use App\Services\Concerns\Complementarios\CatalogoComplementarioImport\HandlesCatalogoComplementarioImportConversionHelpers;
use App\Services\Concerns\Complementarios\CatalogoComplementarioImport\HandlesCatalogoComplementarioImportMappingHelpers;
use App\Services\Concerns\Complementarios\CatalogoComplementarioImport\HandlesCatalogoComplementarioImportUpsertHelpers;

class CatalogoComplementarioImportService
{
    use HandlesCatalogoComplementarioImportActions;
    use HandlesCatalogoComplementarioImportConversionHelpers;
    use HandlesCatalogoComplementarioImportMappingHelpers;
    use HandlesCatalogoComplementarioImportUpsertHelpers;

    private const NIVEL_CURSO_ESPECIAL = 'CURSO ESPECIAL';
}
