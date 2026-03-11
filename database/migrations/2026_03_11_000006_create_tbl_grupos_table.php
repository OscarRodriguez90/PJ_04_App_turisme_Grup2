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
        Schema::create('tbl_grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_grupo', 100);
            $table->string('codigo_invitacion', 10)->unique();
            $table->unsignedBigInteger('id_creador');

            $table->foreign('id_creador')->references('id')->on('tbl_usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_grupos');
    }
};
