<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aitg_planes_contratacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('modalidad', ['regular', 'virtual', 'fic']);
            $table->foreignId('regional_id')->constrained('regionals')->restrictOnDelete();
            $table->string('periodo', 20);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('estado', ['borrador', 'activo', 'cerrado'])->default('borrador');
            $table->decimal('tope_global', 10, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('user_create_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_update_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('aitg_perfiles_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_contratacion_id')
                ->constrained('aitg_planes_contratacion')
                ->cascadeOnDelete();
            $table->string('nombre_programa');
            $table->string('codigo_programa', 50);
            $table->enum('nivel_formacion', ['auxiliar', 'tecnico', 'tecnologo', 'complementaria']);
            $table->string('tipo_contrato');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('aitg_alternativas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfil_plan_id')
                ->constrained('aitg_perfiles_plan')
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('numero');
            $table->string('nombre')->nullable();
            $table->timestamps();

            $table->unique(['perfil_plan_id', 'numero']);
        });

        Schema::create('aitg_requisitos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternativa_id')
                ->constrained('aitg_alternativas')
                ->cascadeOnDelete();
            $table->enum('tipo_requisito', ['academico', 'experiencia', 'competencia', 'otro']);
            $table->text('descripcion');
            $table->decimal('puntaje_base', 8, 2)->default(0);
            $table->boolean('es_obligatorio')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('aitg_puntos_adicionales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_contratacion_id')
                ->constrained('aitg_planes_contratacion')
                ->cascadeOnDelete();
            $table->string('nombre_item');
            $table->decimal('puntaje_adicional', 8, 2);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aitg_puntos_adicionales');
        Schema::dropIfExists('aitg_requisitos');
        Schema::dropIfExists('aitg_alternativas');
        Schema::dropIfExists('aitg_perfiles_plan');
        Schema::dropIfExists('aitg_planes_contratacion');
    }
};
