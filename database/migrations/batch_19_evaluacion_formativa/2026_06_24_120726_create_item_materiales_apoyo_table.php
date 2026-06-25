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
        Schema::create('item_materiales_apoyo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('item_id')->on('item_evaluable')->onDelete('cascade');
            $table->unsignedBigInteger('material_apoyo_id');
            $table->foreign('material_apoyo_id')->references('id')->on('materiales_apoyo')->onDelete('cascade');
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
        Schema::dropIfExists('item_materiales_apoyo');
    }
};
