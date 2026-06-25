<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aitg_planes_contratacion', function (Blueprint $table) {
            if (! Schema::hasColumn('aitg_planes_contratacion', 'competencia_id')) {
                $table->unsignedBigInteger('competencia_id')->nullable()->after('id');
            }
        });

        if (Schema::hasColumn('aitg_planes_contratacion', 'programa_formacion_id')) {
            DB::table('aitg_planes_contratacion')
                ->whereNotNull('programa_formacion_id')
                ->orderBy('id')
                ->lazy()
                ->each(function ($plan) {
                    $competenciaId = DB::table('competencia_programa')
                        ->where('programa_id', $plan->programa_formacion_id)
                        ->value('competencia_id');

                    if (! $competenciaId) {
                        $competenciaId = DB::table('competencias')
                            ->where('status', 1)
                            ->orderBy('nombre')
                            ->value('id');
                    }

                    if ($competenciaId) {
                        DB::table('aitg_planes_contratacion')
                            ->where('id', $plan->id)
                            ->update(['competencia_id' => $competenciaId]);
                    }
                });
        }

        $programaForeignKeys = collect(DB::select("
            SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'aitg_planes_contratacion'
            AND COLUMN_NAME = 'programa_formacion_id' AND REFERENCED_TABLE_NAME IS NOT NULL
        "));

        Schema::table('aitg_planes_contratacion', function (Blueprint $table) use ($programaForeignKeys) {
            if (Schema::hasColumn('aitg_planes_contratacion', 'programa_formacion_id')) {
                if ($programaForeignKeys->isNotEmpty()) {
                    $table->dropForeign(['programa_formacion_id']);
                }
                $table->dropColumn('programa_formacion_id');
            }
        });

        $defaultCompetenciaId = DB::table('competencias')->where('status', 1)->orderBy('id')->value('id');

        if ($defaultCompetenciaId) {
            DB::table('aitg_planes_contratacion')
                ->whereNull('competencia_id')
                ->update(['competencia_id' => $defaultCompetenciaId]);
        }

        $competenciaForeignKeys = collect(DB::select("
            SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'aitg_planes_contratacion'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME LIKE '%competencia%'
        "));

        if ($competenciaForeignKeys->isEmpty()
            && Schema::hasColumn('aitg_planes_contratacion', 'competencia_id')
            && $defaultCompetenciaId
            && DB::table('aitg_planes_contratacion')->whereNull('competencia_id')->doesntExist()
        ) {
            Schema::table('aitg_planes_contratacion', function (Blueprint $table) {
                $table->foreign('competencia_id')->references('id')->on('competencias')->restrictOnDelete();
            });

            DB::statement('ALTER TABLE aitg_planes_contratacion MODIFY competencia_id BIGINT UNSIGNED NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::table('aitg_planes_contratacion', function (Blueprint $table) {
            if (! Schema::hasColumn('aitg_planes_contratacion', 'programa_formacion_id')) {
                $table->unsignedBigInteger('programa_formacion_id')->nullable()->after('id');
            }
        });

        $defaultProgramaId = DB::table('programas_formacion')->where('status', true)->orderBy('id')->value('id');

        if ($defaultProgramaId) {
            DB::table('aitg_planes_contratacion')->update(['programa_formacion_id' => $defaultProgramaId]);
        }

        $competenciaForeignKeys = collect(DB::select("
            SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'aitg_planes_contratacion'
            AND COLUMN_NAME = 'competencia_id' AND REFERENCED_TABLE_NAME IS NOT NULL
        "));

        Schema::table('aitg_planes_contratacion', function (Blueprint $table) use ($competenciaForeignKeys, $defaultProgramaId) {
            if (Schema::hasColumn('aitg_planes_contratacion', 'competencia_id')) {
                if ($competenciaForeignKeys->isNotEmpty()) {
                    $table->dropForeign(['competencia_id']);
                }
                $table->dropColumn('competencia_id');
            }

            if (Schema::hasColumn('aitg_planes_contratacion', 'programa_formacion_id') && $defaultProgramaId) {
                $table->foreign('programa_formacion_id')->references('id')->on('programas_formacion')->restrictOnDelete();
            }
        });
    }
};
