<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Se añade la FK de id_equipo_ganador a tbl_salas después de crear tbl_equipos
     * para evitar la dependencia circular entre ambas tablas.
     */
    public function up(): void
    {
        Schema::table('tbl_salas', function (Blueprint $table) {
            $table->foreign('id_equipo_ganador')->references('id')->on('tbl_equipos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_salas', function (Blueprint $table) {
            $table->dropForeign(['id_equipo_ganador']);
        });
    }
};
