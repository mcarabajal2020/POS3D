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
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropUnique('articulos_codigo_sku_unique');
            $table->unique(['empresa_id', 'codigo_sku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropUnique(['empresa_id', 'codigo_sku']);
            $table->unique('codigo_sku');
        });
    }
};
