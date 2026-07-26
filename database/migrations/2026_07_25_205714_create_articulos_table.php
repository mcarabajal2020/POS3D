<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_sku')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('filamento_gramos', 8, 2)->default(0);
            $table->unsignedInteger('costo_por_gramo')->default(0);
            $table->decimal('horas_impresion', 8, 2)->default(0);
            $table->unsignedInteger('costo_hora_maquina')->default(0);
            $table->unsignedInteger('energia_electrica')->default(0);
            $table->unsignedInteger('post_procesado')->default(0);
            $table->unsignedInteger('precio_venta')->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};
