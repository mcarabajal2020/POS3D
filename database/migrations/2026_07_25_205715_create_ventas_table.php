<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->cascadeOnDelete();
            $table->date('fecha');
            $table->string('estado')->default('presupuesto');
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('descuento')->default(0);
            $table->string('factura_tipo')->nullable();
            $table->string('factura_numero')->nullable();
            $table->string('factura_cae')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
