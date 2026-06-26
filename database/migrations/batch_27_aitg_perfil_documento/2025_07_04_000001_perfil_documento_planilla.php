<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('aitg_perfiles_plan')) {
            Schema::table('aitg_perfiles_plan', function (Blueprint $table) {
                if (! Schema::hasColumn('aitg_perfiles_plan', 'requiere_documento')) {
                    $table->boolean('requiere_documento')->default(true)->after('experiencia_docencia_meses');
                }
                if (! Schema::hasColumn('aitg_perfiles_plan', 'documento_nombre')) {
                    $table->string('documento_nombre', 255)->nullable()->after('requiere_documento');
                }
                if (! Schema::hasColumn('aitg_perfiles_plan', 'documento_descripcion')) {
                    $table->text('documento_descripcion')->nullable()->after('documento_nombre');
                }
                if (! Schema::hasColumn('aitg_perfiles_plan', 'documento_es_obligatorio')) {
                    $table->boolean('documento_es_obligatorio')->default(false)->after('documento_descripcion');
                }
            });
        }

        if (Schema::hasTable('aitg_postulacion_archivos')) {
            Schema::table('aitg_postulacion_archivos', function (Blueprint $table) {
                if (! Schema::hasColumn('aitg_postulacion_archivos', 'perfil_plan_id')) {
                    $table->foreignId('perfil_plan_id')->nullable()->after('punto_adicional_id')
                        ->constrained('aitg_perfiles_plan')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('aitg_postulacion_archivos') && Schema::hasColumn('aitg_postulacion_archivos', 'perfil_plan_id')) {
            Schema::table('aitg_postulacion_archivos', function (Blueprint $table) {
                $table->dropConstrainedForeignId('perfil_plan_id');
            });
        }

        if (Schema::hasTable('aitg_perfiles_plan')) {
            Schema::table('aitg_perfiles_plan', function (Blueprint $table) {
                foreach (['requiere_documento', 'documento_nombre', 'documento_descripcion', 'documento_es_obligatorio'] as $col) {
                    if (Schema::hasColumn('aitg_perfiles_plan', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
