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
        Schema::create('item_evaluable', function (Blueprint $table) {
            $table->id('item_id');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_limite_entrega')->nullable();
            $table->foreignId('estado')->constrained('parametros');
            $table->foreignId('tipo_actividad')->constrained('parametros_temas');
            $table->foreignId('user_create_id')->constrained('users');
            $table->foreignId('user_update_id')->nullable()->constrained('users');
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
        Schema::dropIfExists('item_evaluable');
    }
};
