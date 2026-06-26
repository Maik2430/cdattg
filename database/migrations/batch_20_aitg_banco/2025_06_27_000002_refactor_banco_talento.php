<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aitg_tipos_archivo', function (Blueprint $table) {
            if (! Schema::hasColumn('aitg_tipos_archivo', 'categoria')) {
                $table->string('categoria', 50)->default('obligatorios_base')->after('descripcion');
            }
            if (! Schema::hasColumn('aitg_tipos_archivo', 'permite_multiples')) {
                $table->boolean('permite_multiples')->default(false)->after('es_obligatorio');
            }
            if (! Schema::hasColumn('aitg_tipos_archivo', 'regla_visibilidad')) {
                $table->string('regla_visibilidad', 50)->default('siempre')->after('permite_multiples');
            }
        });

        if (! Schema::hasTable('aitg_postulaciones_plan')) {
            Schema::create('aitg_postulaciones_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete();
            $table->foreignId('plan_contratacion_id')->constrained('aitg_planes_contratacion')->cascadeOnDelete();
            $table->foreignId('perfil_plan_id')->nullable()->constrained('aitg_perfiles_plan')->nullOnDelete();
            $table->enum('estado', [
                'borrador',
                'pendiente_revision',
                'requiere_correccion',
                'aprobado',
                'rechazado',
            ])->default('borrador');
            $table->timestamp('fecha_envio')->nullable();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->text('observaciones_validador')->nullable();
            $table->foreignId('user_create_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_update_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'plan_contratacion_id'], 'aitg_postulaciones_user_plan_unique');
            });
        }

        if (! Schema::hasTable('aitg_archivos_talento')) {
            Schema::create('aitg_archivos_talento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tipo_archivo_id')->nullable()->constrained('aitg_tipos_archivo')->nullOnDelete();
            $table->foreignId('competencia_id')->nullable()->constrained('competencias')->nullOnDelete();
            $table->foreignId('plan_contratacion_id')->nullable()->constrained('aitg_planes_contratacion')->nullOnDelete();
            $table->foreignId('perfil_plan_id')->nullable()->constrained('aitg_perfiles_plan')->nullOnDelete();
            $table->foreignId('punto_adicional_id')->nullable()->constrained('aitg_puntos_adicionales')->nullOnDelete();
            $table->string('storage_disk', 30)->default('public');
            $table->string('storage_path', 500);
            $table->string('nombre_original', 255);
            $table->string('nombre_almacenado', 255);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->enum('estado', ['pendiente', 'en_revision', 'aprobado', 'rechazado'])->default('pendiente');
            $table->foreignId('user_create_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_update_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            });
        }

        if (! Schema::hasTable('aitg_postulacion_archivos')) {
            Schema::create('aitg_postulacion_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulacion_id')->constrained('aitg_postulaciones_plan')->cascadeOnDelete();
            $table->foreignId('archivo_talento_id')->constrained('aitg_archivos_talento')->cascadeOnDelete();
            $table->foreignId('tipo_archivo_id')->nullable()->constrained('aitg_tipos_archivo')->nullOnDelete();
            $table->foreignId('punto_adicional_id')->nullable()->constrained('aitg_puntos_adicionales')->nullOnDelete();
            $table->enum('estado', ['pendiente', 'en_revision', 'aprobado', 'rechazado'])->default('pendiente');
            $table->timestamps();
            });
        }

        if (Schema::hasTable('aitg_validaciones_documento')) {
            Schema::table('aitg_validaciones_documento', function (Blueprint $table) {
                if (! Schema::hasColumn('aitg_validaciones_documento', 'postulacion_archivo_id')) {
                    $table->foreignId('postulacion_archivo_id')->nullable()->after('documento_id')
                        ->constrained('aitg_postulacion_archivos')->cascadeOnDelete();
                }
            });
        }

        if (Schema::hasTable('aitg_solicitudes_banco')) {
            try {
                Schema::table('aitg_solicitudes_banco', function (Blueprint $table) {
                    $table->dropUnique(['user_id']);
                });
            } catch (\Throwable) {
                // Índice puede estar referenciado por FK legacy; no bloquea el banco de talento.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('aitg_postulacion_archivos');
        Schema::dropIfExists('aitg_archivos_talento');
        Schema::dropIfExists('aitg_postulaciones_plan');

        Schema::table('aitg_tipos_archivo', function (Blueprint $table) {
            $columns = ['categoria', 'permite_multiples', 'regla_visibilidad'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('aitg_tipos_archivo', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('aitg_solicitudes_banco', function (Blueprint $table) {
            $table->unique('user_id');
        });
    }
};
