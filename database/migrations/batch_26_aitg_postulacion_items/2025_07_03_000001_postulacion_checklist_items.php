<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aitg_postulacion_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulacion_id')->constrained('aitg_postulaciones_plan')->cascadeOnDelete();
            $table->foreignId('checklist_plan_id')->nullable()->constrained('aitg_checklist_plan')->nullOnDelete();
            $table->string('nombre', 255);
            $table->text('descripcion_criterio');
            $table->decimal('puntaje', 8, 2)->default(0);
            $table->boolean('es_obligatorio')->default(true);
            $table->foreignId('postulacion_archivo_id')->nullable()->constrained('aitg_postulacion_archivos')->nullOnDelete();
            $table->enum('estado', [
                'pendiente',
                'cargado',
                'pendiente_evaluacion',
                'evaluado',
                'requiere_subsanacion',
            ])->default('pendiente');
            $table->boolean('cumple')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('solicita_actualizacion')->default(false);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->unique(['postulacion_id', 'checklist_plan_id'], 'aitg_post_checklist_unique');
        });

        Schema::create('aitg_postulacion_punto_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulacion_id')->constrained('aitg_postulaciones_plan')->cascadeOnDelete();
            $table->foreignId('punto_adicional_id')->nullable()->constrained('aitg_puntos_adicionales')->nullOnDelete();
            $table->string('descripcion', 500);
            $table->decimal('puntaje_adicional', 8, 2)->default(0);
            $table->boolean('es_opcional')->default(false);
            $table->foreignId('postulacion_archivo_id')->nullable()->constrained('aitg_postulacion_archivos')->nullOnDelete();
            $table->enum('estado', [
                'pendiente',
                'cargado',
                'pendiente_evaluacion',
                'evaluado',
                'requiere_subsanacion',
            ])->default('pendiente');
            $table->boolean('cumple')->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->unique(['postulacion_id', 'punto_adicional_id'], 'aitg_post_punto_unique');
        });

        $this->migrarPostulacionesConvocatoriaExistentes();
    }

    public function down(): void
    {
        Schema::dropIfExists('aitg_postulacion_punto_items');
        Schema::dropIfExists('aitg_postulacion_checklist_items');
    }

    private function migrarPostulacionesConvocatoriaExistentes(): void
    {
        if (! Schema::hasTable('aitg_postulaciones_plan')) {
            return;
        }

        $postulaciones = DB::table('aitg_postulaciones_plan')
            ->whereNotNull('convocatoria_id')
            ->get(['id', 'plan_contratacion_id']);

        foreach ($postulaciones as $post) {
            $checklist = DB::table('aitg_checklist_plan')
                ->where('plan_contratacion_id', $post->plan_contratacion_id)
                ->orderBy('orden')
                ->get();

            foreach ($checklist as $index => $item) {
                DB::table('aitg_postulacion_checklist_items')->insertOrIgnore([
                    'postulacion_id' => $post->id,
                    'checklist_plan_id' => $item->id,
                    'nombre' => $item->nombre ?? mb_substr($item->descripcion_criterio, 0, 255),
                    'descripcion_criterio' => $item->descripcion_criterio,
                    'puntaje' => $item->puntaje ?? 10,
                    'es_obligatorio' => $item->es_obligatorio ?? true,
                    'estado' => 'pendiente',
                    'orden' => $item->orden ?? ($index + 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $puntos = DB::table('aitg_puntos_adicionales')
                ->where('plan_contratacion_id', $post->plan_contratacion_id)
                ->orderBy('orden')
                ->get();

            foreach ($puntos as $index => $punto) {
                $archivoId = DB::table('aitg_postulacion_archivos')
                    ->where('postulacion_id', $post->id)
                    ->where('punto_adicional_id', $punto->id)
                    ->value('id');

                DB::table('aitg_postulacion_punto_items')->insertOrIgnore([
                    'postulacion_id' => $post->id,
                    'punto_adicional_id' => $punto->id,
                    'descripcion' => $punto->descripcion,
                    'puntaje_adicional' => $punto->puntaje_adicional ?? 0,
                    'postulacion_archivo_id' => $archivoId,
                    'estado' => $archivoId ? 'cargado' : 'pendiente',
                    'orden' => $punto->orden ?? ($index + 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
