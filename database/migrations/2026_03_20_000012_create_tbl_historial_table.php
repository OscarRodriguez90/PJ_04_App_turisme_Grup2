<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_historial', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sala');
            $table->unsignedBigInteger('id_equipo');
            $table->unsignedBigInteger('id_usuario');
            $table->enum('resultado', ['victoria', 'derrota']);
            $table->integer('tiempo_total_segundos')->default(0);
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->json('retos_completados'); // snapshot of reto order, name, fecha_completado
            $table->timestamps();

            $table->foreign('id_sala')->references('id')->on('tbl_salas')->onDelete('cascade');
            $table->foreign('id_equipo')->references('id')->on('tbl_equipos')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id')->on('tbl_usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_historial');
    }
};
