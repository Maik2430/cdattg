<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('aitg_postulaciones_plan')) {
            return;
        }

        DB::table('aitg_postulaciones_plan')
            ->whereNull('convocatoria_id')
            ->where('estado', 'preseleccionado')
            ->update([
                'estado' => 'aprobado',
                'fase_actual' => 'inicial',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // No revertir: el estado preseleccionado en banco era incorrecto.
    }
};
