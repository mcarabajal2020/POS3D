<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacion_historials', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('mensaje');
            $table->string('tipo')->default('info');
            $table->string('destino');
            $table->json('empresas_ids')->nullable();
            $table->unsignedInteger('cantidad_usuarios');
            $table->foreignId('enviada_por')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion_historials');
    }
};
