<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('aitg_requisitos');
        Schema::dropIfExists('aitg_alternativas');
        Schema::dropIfExists('aitg_perfiles_plan');

        Schema::table('aitg_planes_contratacion', function (Blueprint $table) {
            if (Schema::hasColumn('aitg_planes_contratacion', 'nombre')) {
                $table->dropColumn('nombre');
            }
        });

        Schema::table('aitg_planes_contratacion', function (Blueprint $table) {
            if (! Schema::hasColumn('aitg_planes_contratacion', 'programa_formacion_id')) {
                $table->foreignId('programa_formacion_id')
                    ->after('id')
                    ->constrained('programas_formacion')
                    ->restrictOnDelete();
            }
            if (! Schema::hasColumn('aitg_planes_contratacion', 'tipo_registro_perfil')) {
                $table->enum('tipo_registro_perfil', ['opcion', 'alternativa', 'directo'])
                    ->default('directo')
                    ->after('programa_formacion_id');
            }
        });

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

        if (Schema::hasColumn('aitg_puntos_adicionales', 'nombre_item')) {
            Schema::table('aitg_puntos_adicionales', function (Blueprint $table) {
                $table->renameColumn('nombre_item', 'descripcion');
            });
        }

        Schema::table('aitg_puntos_adicionales', function (Blueprint $table) {
            if (! Schema::hasColumn('aitg_puntos_adicionales', 'consecutivo')) {
                $table->unsignedSmallInteger('consecutivo')->default(1)->after('plan_contratacion_id');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aitg_perfiles_plan');

        Schema::table('aitg_planes_contratacion', function (Blueprint $table) {
            $table->dropForeign(['programa_formacion_id']);
            $table->dropColumn(['programa_formacion_id', 'tipo_registro_perfil']);
            $table->string('nombre')->nullable();
        });

        Schema::create('aitg_perfiles_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_contratacion_id')->constrained('aitg_planes_contratacion')->cascadeOnDelete();
            $table->string('nombre_programa');
            $table->string('codigo_programa', 50);
            $table->enum('nivel_formacion', ['auxiliar', 'tecnico', 'tecnologo', 'complementaria']);
            $table->string('tipo_contrato');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }
};
