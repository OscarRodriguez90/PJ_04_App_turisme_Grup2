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
        Schema::create('tbl_equipos', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_equipo');
            $table->string('nombre_equipo', 50);
            $table->unsignedBigInteger('id_lider');
            
            $table->foreign('id_lider')->references('id')->on('tbl_usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_equipos');
    }
};
