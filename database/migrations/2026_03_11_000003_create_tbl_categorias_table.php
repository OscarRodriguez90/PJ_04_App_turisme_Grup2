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
        Schema::create('tbl_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->string('icono_url', 255)->nullable();
            $table->string('color_marcador', 7)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_categorias');
    }
};
