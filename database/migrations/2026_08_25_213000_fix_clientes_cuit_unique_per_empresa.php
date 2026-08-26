<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropUnique('clientes_cuit_cuil_unique');
            $table->unique(['empresa_id', 'cuit_cuil']);
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropUnique(['empresa_id', 'cuit_cuil']);
            $table->unique('cuit_cuil');
        });
    }
};
