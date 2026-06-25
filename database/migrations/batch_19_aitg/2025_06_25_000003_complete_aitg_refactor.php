<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('aitg_requisitos')) {
            Schema::dropIfExists('aitg_requisitos');
        }
        if (Schema::hasTable('aitg_alternativas')) {
            Schema::dropIfExists('aitg_alternativas');
        }
        if (Schema::hasTable('aitg_perfiles_plan')) {
            Schema::dropIfExists('aitg_perfiles_plan');
        }

        if (Schema::hasColumn('aitg_planes_contratacion', 'nombre')) {
            Schema::table('aitg_planes_contratacion', function (Blueprint $table) {
                $table->dropColumn('nombre');
            });
        }

        Schema::table('aitg_planes_contratacion', function (Blueprint $table) {
            if (! Schema::hasColumn('aitg_planes_contratacion', 'programa_formacion_id')) {
                $table->unsignedBigInteger('programa_formacion_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('aitg_planes_contratacion', 'tipo_registro_perfil')) {
                $table->enum('tipo_registro_perfil', ['opcion', 'alternativa', 'directo'])
                    ->default('directo')
                    ->after('id');
            }
        });

        $defaultProgramaId = DB::table('programas_formacion')->where('status', true)->value('id')
            ?? DB::table('programas_formacion')->value('id');

        if ($defaultProgramaId) {
            DB::table('aitg_planes_contratacion')
                ->whereNull('programa_formacion_id')
                ->orWhere('programa_formacion_id', 0)
                ->update(['programa_formacion_id' => $defaultProgramaId]);
        } else {
            DB::table('aitg_planes_contratacion')->where('programa_formacion_id', 0)->delete();
        }

        $invalidPlanIds = DB::table('aitg_planes_contratacion')
            ->whereNotIn('programa_formacion_id', DB::table('programas_formacion')->pluck('id'))
            ->pluck('id');
        if ($invalidPlanIds->isNotEmpty()) {
            DB::table('aitg_planes_contratacion')->whereIn('id', $invalidPlanIds)->delete();
        }

        if (Schema::hasColumn('aitg_planes_contratacion', 'programa_formacion_id') && $defaultProgramaId) {
            DB::statement('ALTER TABLE aitg_planes_contratacion MODIFY programa_formacion_id BIGINT UNSIGNED NOT NULL');
        }

        $foreignKeys = collect(DB::select("
            SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'aitg_planes_contratacion'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME LIKE '%programa_formacion%'
        "));

        if ($foreignKeys->isEmpty() && Schema::hasColumn('aitg_planes_contratacion', 'programa_formacion_id') && $defaultProgramaId) {
            Schema::table('aitg_planes_contratacion', function (Blueprint $table) {
                $table->foreign('programa_formacion_id')
                    ->references('id')
                    ->on('programas_formacion')
                    ->restrictOnDelete();
            });
        }

        if (! Schema::hasTable('aitg_perfiles_plan')) {
            Schema::create('aitg_perfiles_plan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('plan_contratacion_id')
                    ->constrained('aitg_planes_contratacion')
                    ->cascadeOnDelete();
                $table->unsignedSmallInteger('consecutivo');
                $table->foreignId('nivel_formacion_id')->constrained('parametros')->restrictOnDelete();
                $table->foreignId('programa_formacion_id')->constrained('programas_formacion')->restrictOnDelete();
                $table->unsignedInteger('experiencia_relacionada_meses')->default(0);
                $table->unsignedInteger('experiencia_docencia_meses')->default(0);
                $table->timestamps();
                $table->unique(['plan_contratacion_id', 'consecutivo']);
            });
        }

        if (Schema::hasColumn('aitg_puntos_adicionales', 'nombre_item')
            && ! Schema::hasColumn('aitg_puntos_adicionales', 'descripcion')) {
            Schema::table('aitg_puntos_adicionales', function (Blueprint $table) {
                $table->renameColumn('nombre_item', 'descripcion');
            });
        }

        Schema::table('aitg_puntos_adicionales', function (Blueprint $table) {
            if (! Schema::hasColumn('aitg_puntos_adicionales', 'consecutivo')) {
                $table->unsignedSmallInteger('consecutivo')->default(1)->after('plan_contratacion_id');
            }
        });

        DB::table('aitg_puntos_adicionales')->orderBy('id')->get()->each(function ($punto, $index) {
            DB::table('aitg_puntos_adicionales')->where('id', $punto->id)->update([
                'consecutivo' => $index + 1,
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aitg_perfiles_plan');
    }
};
