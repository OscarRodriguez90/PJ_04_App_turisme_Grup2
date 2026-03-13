<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_favoritos', function (Blueprint $table) {
            $table->unique(['id_usuario', 'id_lugar'], 'tbl_favoritos_usuario_lugar_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_favoritos', function (Blueprint $table) {
            $table->dropUnique('tbl_favoritos_usuario_lugar_unique');
        });
    }
};
