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
        Schema::create('rap_item_evaluable', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_evaluable_id');
            $table->foreign('item_evaluable_id')->references('item_id')->on('item_evaluable')->onDelete('cascade');
            $table->unsignedBigInteger('rap_id');
            $table->foreign('rap_id')->references('id')->on('resultados_aprendizajes')->onDelete('cascade');
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
        Schema::dropIfExists('rap_item_evaluable');
    }
};
