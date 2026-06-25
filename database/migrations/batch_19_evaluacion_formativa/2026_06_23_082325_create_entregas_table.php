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
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('item_evaluable', 'item_id')->onDelete('cascade');
            $table->foreignId('aprendiz_id')->constrained('aprendices')->onDelete('cascade');
            $table->boolean('juicio')->nullable();
            $table->foreignId('estado')->constrained('parametros');
            $table->text('observacion_instructor')->nullable();
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
        Schema::dropIfExists('entregas');
    }
};
