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
        Schema::create('criterios_rubricas_item_evaluable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_evaluable_id')->constrained('item_evaluable', 'item_id');
            $table->foreignId('rubricas_criterios_id')->constrained('rubricas_criterios');
            $table->double('peso_porcentual', 8, 2);
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
        Schema::dropIfExists('criterios_rubricas_item_evaluable');
    }
};
