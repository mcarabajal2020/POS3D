<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('articulo_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('cantidad')->default(1);
            $table->unsignedInteger('precio_unitario')->default(0);
            $table->unsignedInteger('subtotal')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_items');
    }
};
