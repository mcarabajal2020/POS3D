<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->decimal('desgaste_maquina', 10, 2)->default(120)->change();
            $table->renameColumn('margen_ganancia', 'multiplicador_precio');
        });
    }

    public function down(): void
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->unsignedInteger('desgaste_maquina')->default(120)->change();
            $table->renameColumn('multiplicador_precio', 'margen_ganancia');
        });
    }
};
