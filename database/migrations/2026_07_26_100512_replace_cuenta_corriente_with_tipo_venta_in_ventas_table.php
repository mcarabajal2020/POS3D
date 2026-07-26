<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('tipo_venta')->default('contado')->after('descuento');
            $table->dropColumn('cuenta_corriente');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->boolean('cuenta_corriente')->default(false)->after('descuento');
            $table->dropColumn('tipo_venta');
        });
    }
};
