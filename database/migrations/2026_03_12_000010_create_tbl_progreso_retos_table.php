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
        Schema::create('tbl_progreso_retos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_reto');
            $table->boolean('completado')->default(false);
            $table->timestamp('fecha_completado')->nullable();

            $table->foreign('id_usuario')->references('id')->on('tbl_usuarios');
            $table->foreign('id_reto')->references('id')->on('tbl_retos');
            $table->unique(['id_usuario', 'id_reto']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_progreso_retos');
    }
};
