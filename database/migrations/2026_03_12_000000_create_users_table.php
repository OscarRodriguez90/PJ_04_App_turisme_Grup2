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
        // Tabla de roles
        Schema::create('tbl_roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_rol', 30)->unique();
        });

        // Tabla de usuarios
        Schema::create('tbl_usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('username', 25)->unique();
            $table->string('nombre', 25);
            $table->string('apellido1', 50);
            $table->string('apellido2', 50)->nullable();
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->string('foto', 255)->nullable();
            $table->unsignedBigInteger('id_rol')->default(2);
            $table->timestamp('fecha_registro')->useCurrent();

            $table->foreign('id_rol')->references('id')->on('tbl_roles');
        });

        // Tablas requeridas por Laravel (sessions, password_reset_tokens)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('tbl_usuarios');
        Schema::dropIfExists('tbl_roles');
    }
};
