<?php

namespace App\Console\Commands\Concerns\CleanDuplicateRoles;

use Illuminate\Support\Facades\DB;

trait HandlesCleanDuplicateRolesReportActions
{
    protected function generateReport(): void
    {
        $this->info('📋 REPORTE FINAL DE ROLES:');
        $this->line(str_repeat('=', 50));

        $roleStats = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('COUNT(*) as count'))
            ->groupBy('roles.name')
            ->orderBy('count', 'desc')
            ->get();

        foreach ($roleStats as $stat) {
            $this->line("   {$stat->name}: {$stat->count} usuarios");
        }

        $this->newLine();
    }
}
