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
        Schema::create('tbl_progreso_gimcana', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_prueba');
            $table->boolean('completado')->default(false);
            $table->timestamp('fecha_validacion')->useCurrent();

            $table->foreign('id_usuario')->references('id')->on('tbl_usuarios');
            $table->foreign('id_prueba')->references('id')->on('tbl_pruebas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_progreso_gimcana');
    }
};
