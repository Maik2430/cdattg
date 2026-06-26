<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Banco de Talento: postulación por competencia (no por plan de contratación). */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('aitg_postulaciones_plan')) {
            return;
        }

        Schema::table('aitg_postulaciones_plan', function (Blueprint $table) {
            if (! Schema::hasColumn('aitg_postulaciones_plan', 'competencia_id')) {
                $table->foreignId('competencia_id')
                    ->nullable()
                    ->after('persona_id')
                    ->constrained('competencias')
                    ->nullOnDelete();
            }
        });

        DB::table('aitg_postulaciones_plan as p')
            ->join('aitg_planes_contratacion as pl', 'pl.id', '=', 'p.plan_contratacion_id')
            ->whereNull('p.competencia_id')
            ->update(['p.competencia_id' => DB::raw('pl.competencia_id')]);

        DB::table('aitg_postulaciones_plan as p')
            ->join('aitg_convocatorias as c', 'c.id', '=', 'p.convocatoria_id')
            ->whereNull('p.competencia_id')
            ->update(['p.competencia_id' => DB::raw('c.competencia_id')]);

        Schema::table('aitg_postulaciones_plan', function (Blueprint $table) {
            if (Schema::hasColumn('aitg_postulaciones_plan', 'plan_contratacion_id')) {
                $table->dropForeign(['plan_contratacion_id']);
            }
        });

        DB::statement('ALTER TABLE aitg_postulaciones_plan MODIFY plan_contratacion_id BIGINT UNSIGNED NULL');

        Schema::table('aitg_postulaciones_plan', function (Blueprint $table) {
            $table->foreign('plan_contratacion_id')
                ->references('id')
                ->on('aitg_planes_contratacion')
                ->nullOnDelete();
        });

        DB::table('aitg_postulaciones_plan')
            ->whereNull('convocatoria_id')
            ->update(['plan_contratacion_id' => null]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('aitg_postulaciones_plan')) {
            return;
        }

        Schema::table('aitg_postulaciones_plan', function (Blueprint $table) {
            if (Schema::hasColumn('aitg_postulaciones_plan', 'competencia_id')) {
                $table->dropConstrainedForeignId('competencia_id');
            }
        });
    }
};
