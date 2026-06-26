<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('aitg_checklist_plan')) {
            Schema::table('aitg_checklist_plan', function (Blueprint $table) {
                if (! Schema::hasColumn('aitg_checklist_plan', 'nombre')) {
                    $table->string('nombre', 255)->nullable()->after('consecutivo');
                }
                if (! Schema::hasColumn('aitg_checklist_plan', 'puntaje')) {
                    $table->decimal('puntaje', 8, 2)->default(10)->after('descripcion_criterio');
                }
                if (! Schema::hasColumn('aitg_checklist_plan', 'es_obligatorio')) {
                    $table->boolean('es_obligatorio')->default(true)->after('puntaje');
                }
            });

            DB::table('aitg_checklist_plan')
                ->whereNull('nombre')
                ->update(['nombre' => DB::raw('LEFT(descripcion_criterio, 255)')]);
        }

        Schema::create('aitg_evaluaciones_postulacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulacion_id')->unique()->constrained('aitg_postulaciones_plan')->cascadeOnDelete();
            $table->enum('estado', [
                'pendiente',
                'en_evaluacion',
                'requiere_subsanacion',
                'aprobado',
                'rechazado',
            ])->default('pendiente');
            $table->decimal('puntaje_checklist', 10, 2)->default(0);
            $table->decimal('puntaje_adicionales', 10, 2)->default(0);
            $table->decimal('puntaje_total', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->foreignId('evaluador_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_finalizacion')->nullable();
            $table->timestamps();
        });

        Schema::create('aitg_evaluacion_checklist_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_id')->constrained('aitg_evaluaciones_postulacion')->cascadeOnDelete();
            $table->foreignId('checklist_plan_id')->constrained('aitg_checklist_plan')->cascadeOnDelete();
            $table->boolean('cumple')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('solicita_actualizacion')->default(false);
            $table->timestamps();

            $table->unique(['evaluacion_id', 'checklist_plan_id'], 'aitg_eval_checklist_unique');
        });

        Schema::create('aitg_evaluacion_puntos_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_id')->constrained('aitg_evaluaciones_postulacion')->cascadeOnDelete();
            $table->foreignId('punto_adicional_id')->constrained('aitg_puntos_adicionales')->cascadeOnDelete();
            $table->boolean('cumple')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['evaluacion_id', 'punto_adicional_id'], 'aitg_eval_puntos_unique');
        });

        if (Schema::hasTable('aitg_postulaciones_plan')) {
            DB::statement("ALTER TABLE aitg_postulaciones_plan MODIFY COLUMN estado ENUM(
                'borrador',
                'pendiente_revision',
                'requiere_correccion',
                'preseleccionado',
                'evaluacion_aprobada',
                'seleccionado',
                'suplente',
                'aprobado',
                'rechazado'
            ) NOT NULL DEFAULT 'borrador'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('aitg_evaluacion_puntos_respuestas');
        Schema::dropIfExists('aitg_evaluacion_checklist_respuestas');
        Schema::dropIfExists('aitg_evaluaciones_postulacion');

        if (Schema::hasTable('aitg_checklist_plan')) {
            Schema::table('aitg_checklist_plan', function (Blueprint $table) {
                foreach (['nombre', 'puntaje', 'es_obligatorio'] as $col) {
                    if (Schema::hasColumn('aitg_checklist_plan', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
