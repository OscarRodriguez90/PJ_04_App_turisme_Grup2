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
        Schema::create('tbl_favoritos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_lugar');
            $table->timestamp('fecha_agregado')->useCurrent();

            $table->foreign('id_usuario')->references('id')->on('tbl_usuarios');
            $table->foreign('id_lugar')->references('id')->on('tbl_lugares');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_favoritos');
    }
};
