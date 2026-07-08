<?php

namespace App\Livewire\Instructores\Concerns;

use App\Models\RedConocimiento;
use App\Models\Regional;
use App\Services\InstructorBusinessRulesService;
use App\Services\InstructorService;

trait HandlesInstructorIndexMountHelpers
{
    protected $instructorService;

    protected $businessRulesService;

    public function boot(InstructorService $instructorService, InstructorBusinessRulesService $businessRulesService)
    {
        $this->instructorService = $instructorService;
        $this->businessRulesService = $businessRulesService;
    }

    private function cargarDatosFiltros()
    {
        $this->regionales = Regional::where('status', true)
            ->orderBy('nombre')
            ->pluck('nombre', 'id')
            ->toArray();

        $this->especialidades = RedConocimiento::where('status', true)
            ->orderBy('nombre')
            ->pluck('nombre', 'id')
            ->toArray();
    }
}
