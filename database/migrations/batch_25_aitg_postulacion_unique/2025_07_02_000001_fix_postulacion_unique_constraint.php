<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * El índice (user_id, plan_contratacion_id) impedía crear postulaciones a convocatoria
 * cuando ya existía la del Banco de Talento (mismo plan).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('aitg_postulaciones_plan')) {
            return;
        }

        if ($this->indexExists('aitg_postulaciones_plan', 'aitg_postulaciones_user_plan_unique')) {
            Schema::table('aitg_postulaciones_plan', function (Blueprint $table) {
                $table->dropUnique('aitg_postulaciones_user_plan_unique');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('aitg_postulaciones_plan')) {
            return;
        }

        if (! $this->indexExists('aitg_postulaciones_plan', 'aitg_postulaciones_user_plan_unique')) {
            Schema::table('aitg_postulaciones_plan', function (Blueprint $table) {
                $table->unique(['user_id', 'plan_contratacion_id'], 'aitg_postulaciones_user_plan_unique');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select('SHOW INDEX FROM '.$table.' WHERE Key_name = ?', [$indexName]);

        return count($indexes) > 0;
    }
};
