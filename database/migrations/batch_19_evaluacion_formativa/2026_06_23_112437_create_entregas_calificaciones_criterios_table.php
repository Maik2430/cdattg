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
        Schema::create('entregas_calificaciones_criterios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_id')->constrained('entregas');
            $table->foreignId('rubrica_criterio_id')->constrained('rubricas_criterios');
            $table->boolean('juicio')->default(false);
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
        Schema::dropIfExists('entregas_calificaciones_criterios');
    }
};
