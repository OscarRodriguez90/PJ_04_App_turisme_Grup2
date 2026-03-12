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
        Schema::create('tbl_salas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_sala', 8)->unique();
            $table->unsignedBigInteger('id_creador');
            $table->enum('estado', ['esperando', 'jugando', 'finalizada'])->default('esperando');
            $table->timestamp('fecha_creacion')->useCurrent();

            $table->foreign('id_creador')->references('id')->on('tbl_usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_salas');
    }
};
