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
        Schema::create('tbl_pruebas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lugar');
            $table->integer('orden');
            $table->text('pregunta');
            $table->string('respuesta_correcta', 255);
            $table->text('pista_siguiente')->nullable();

            $table->foreign('id_lugar')->references('id')->on('tbl_lugares');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_pruebas');
    }
};
