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
        Schema::create('tbl_retos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sala');
            $table->unsignedBigInteger('id_lugar');
            $table->integer('orden');
            $table->text('pista');
            $table->text('pregunta');
            $table->string('respuesta_correcta', 255);

            $table->foreign('id_sala')->references('id')->on('tbl_salas');
            $table->foreign('id_lugar')->references('id')->on('tbl_lugares');
            $table->unique(['id_sala', 'orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_retos');
    }
};
