<?php

namespace App\Services;

use App\Repositories\AprendizRepository;
use App\Services\Concerns\Import\HandlesImportAprendicesActions;
use App\Services\Concerns\Import\HandlesImportInstructoresActions;

class ImportService
{
    use HandlesImportAprendicesActions;
    use HandlesImportInstructoresActions;

    protected AprendizRepository $aprendizRepo;

    public function __construct(AprendizRepository $aprendizRepo)
    {
        $this->aprendizRepo = $aprendizRepo;
    }
}
