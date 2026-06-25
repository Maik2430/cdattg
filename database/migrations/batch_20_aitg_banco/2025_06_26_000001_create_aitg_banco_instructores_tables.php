<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aitg_tipos_archivo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->json('extensiones_permitidas')->nullable();
            $table->unsignedInteger('tamano_max_kb')->default(5120);
            $table->boolean('es_obligatorio')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->foreignId('user_create_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_update_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('aitg_motivos_rechazo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->foreignId('user_create_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_update_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('aitg_solicitudes_banco', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete();
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
        });

        Schema::create('aitg_documentos_banco', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('aitg_solicitudes_banco')->cascadeOnDelete();
            $table->foreignId('tipo_archivo_id')->constrained('aitg_tipos_archivo')->restrictOnDelete();
            $table->string('storage_disk', 30)->default('public');
            $table->string('storage_path', 500);
            $table->string('nombre_original', 255);
            $table->string('nombre_almacenado', 255);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->enum('estado', [
                'pendiente',
                'en_revision',
                'aprobado',
                'rechazado',
            ])->default('pendiente');
            $table->foreignId('user_create_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_update_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['solicitud_id', 'tipo_archivo_id']);
        });

        Schema::create('aitg_validaciones_documento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('aitg_documentos_banco')->cascadeOnDelete();
            $table->foreignId('validador_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('resultado', ['aprobado', 'rechazado']);
            $table->foreignId('motivo_rechazo_id')->nullable()->constrained('aitg_motivos_rechazo')->nullOnDelete();
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha_validacion');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aitg_validaciones_documento');
        Schema::dropIfExists('aitg_documentos_banco');
        Schema::dropIfExists('aitg_solicitudes_banco');
        Schema::dropIfExists('aitg_motivos_rechazo');
        Schema::dropIfExists('aitg_tipos_archivo');
    }
};
