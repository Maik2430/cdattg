<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('materiales_apoyo', function (Blueprint $table) {
            $table->id(); // PK: bigint unsigned
            
            $table->string('titulo');
            $table->text('descripcion');
            $table->unsignedBigInteger('tipo_material_id');
            $table->foreign('tipo_material_id')->references('id')->on('parametros_temas')->onDelete('cascade');
            $table->string('archivo_ruta')->nullable();
            $table->string('archivo_url')->nullable();
            $table->string('mime_type');
            $table->string('extension');
            $table->bigInteger('tamano_bytes');
            $table->boolean('estado')->default(true);
            $table->foreignId('user_create_id')->constrained('users');
            $table->foreignId('user_edit_id')->nullable()->constrained('users');
            $table->foreignId('user_delete_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiales_apoyo');
    }
};
