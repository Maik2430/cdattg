<?php

namespace App\Policies\Concerns\GuiaAprendizaje;

use App\Models\GuiasAprendizaje;
use App\Models\User;

trait ReportePolicy
{
    /**
     * Determine whether the user can view guía progress report.
     */
    public function reporteProgreso(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->view($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can view guías statistics.
     */
    public function estadisticas(User $user): bool
    {
        return $user->can('VER GUIA APRENDIZAJE');
    }

    /**
     * Determine whether the user can export guía to PDF.
     */
    public function exportarPdf(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->view($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can export guías to Excel.
     */
    public function exportarExcel(User $user): bool
    {
        return $user->can('VER GUIA APRENDIZAJE') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }
}
