<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aitg_convocatorias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('titulo', 255);
            $table->foreignId('competencia_id')->constrained('competencias')->cascadeOnDelete();
            $table->foreignId('plan_contratacion_id')->constrained('aitg_planes_contratacion')->cascadeOnDelete();
            $table->text('descripcion')->nullable();
            $table->text('objeto_contractual')->nullable();
            $table->text('requisitos')->nullable();
            $table->enum('estado', ['borrador', 'publicada', 'cerrada', 'finalizada'])->default('borrador');
            $table->string('codigo_cdp', 100)->nullable();
            $table->decimal('valor_total', 15, 2)->nullable();
            $table->decimal('valor_contrato_honorarios', 15, 2)->nullable();
            $table->date('fecha_inicio_publicacion')->nullable();
            $table->date('fecha_fin_publicacion')->nullable();
            $table->date('fecha_inicio_contrato')->nullable();
            $table->date('fecha_fin_contrato')->nullable();
            $table->foreignId('centro_formacion_id')->nullable()->constrained('centro_formacions')->nullOnDelete();
            $table->foreignId('regional_id')->nullable()->constrained('regionals')->nullOnDelete();
            $table->foreignId('programa_formacion_id')->nullable()->constrained('programas_formacion')->nullOnDelete();
            $table->timestamp('fecha_publicacion')->nullable();
            $table->foreignId('user_create_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_update_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        if (Schema::hasTable('aitg_postulaciones_plan')) {
            Schema::table('aitg_postulaciones_plan', function (Blueprint $table) {
                if (! Schema::hasColumn('aitg_postulaciones_plan', 'convocatoria_id')) {
                    $table->foreignId('convocatoria_id')->nullable()->after('plan_contratacion_id')
                        ->constrained('aitg_convocatorias')->nullOnDelete();
                    $table->unique(['user_id', 'convocatoria_id'], 'aitg_postulaciones_user_convocatoria_unique');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('aitg_postulaciones_plan') && Schema::hasColumn('aitg_postulaciones_plan', 'convocatoria_id')) {
            Schema::table('aitg_postulaciones_plan', function (Blueprint $table) {
                $table->dropUnique('aitg_postulaciones_user_convocatoria_unique');
                $table->dropConstrainedForeignId('convocatoria_id');
            });
        }

        Schema::dropIfExists('aitg_convocatorias');
    }
};
