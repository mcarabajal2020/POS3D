<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('precio_mensual');
            $table->integer('precio_anual')->nullable();
            $table->integer('max_usuarios')->default(2);
            $table->integer('max_ventas_mensuales')->default(100);
            $table->integer('max_articulos')->default(50);
            $table->integer('max_filamentos')->default(10);
            $table->integer('max_impresoras')->default(3);
            $table->integer('trial_dias')->default(14);
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
