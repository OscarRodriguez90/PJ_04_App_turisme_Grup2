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
        Schema::create('tbl_usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('username', 25)->unique();
            $table->string('nombre', 25);
            $table->string('apellido1', 50);
            $table->string('apellido2', 50)->nullable();
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->unsignedBigInteger('id_rol')->default(2);
            $table->timestamp('fecha_registro')->useCurrent();

            $table->foreign('id_rol')->references('id')->on('tbl_roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_usuarios');
    }
};
