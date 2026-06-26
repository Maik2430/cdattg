<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('aitg_tipos_archivo') && ! Schema::hasColumn('aitg_tipos_archivo', 'fase_carga')) {
            Schema::table('aitg_tipos_archivo', function (Blueprint $table) {
                $table->string('fase_carga', 30)->default('inicial')->after('regla_visibilidad');
            });
        }

        if (Schema::hasTable('aitg_postulaciones_plan')) {
            Schema::table('aitg_postulaciones_plan', function (Blueprint $table) {
                if (! Schema::hasColumn('aitg_postulaciones_plan', 'fase_actual')) {
                    $table->string('fase_actual', 30)->default('inicial')->after('estado');
                }
            });

            DB::statement("ALTER TABLE aitg_postulaciones_plan MODIFY COLUMN estado ENUM(
                'borrador',
                'pendiente_revision',
                'requiere_correccion',
                'preseleccionado',
                'aprobado',
                'rechazado'
            ) NOT NULL DEFAULT 'borrador'");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('aitg_tipos_archivo', 'fase_carga')) {
            Schema::table('aitg_tipos_archivo', function (Blueprint $table) {
                $table->dropColumn('fase_carga');
            });
        }

        if (Schema::hasTable('aitg_postulaciones_plan')) {
            if (Schema::hasColumn('aitg_postulaciones_plan', 'fase_actual')) {
                Schema::table('aitg_postulaciones_plan', function (Blueprint $table) {
                    $table->dropColumn('fase_actual');
                });
            }

            DB::statement("ALTER TABLE aitg_postulaciones_plan MODIFY COLUMN estado ENUM(
                'borrador',
                'pendiente_revision',
                'requiere_correccion',
                'aprobado',
                'rechazado'
            ) NOT NULL DEFAULT 'borrador'");
        }
    }
};
