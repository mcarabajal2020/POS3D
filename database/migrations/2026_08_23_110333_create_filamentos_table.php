<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedInteger('precio_kg');
            $table->foreignId('empresa_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filamentos');
    }
};
