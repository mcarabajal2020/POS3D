<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('cuit_cuil')->unique();
            $table->text('direccion');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('condicion_iva');
            $table->integer('saldo')->default(0);
            $table->timestamps();
        });

        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_sku')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('tipo_material')->default('PLA');
            $table->integer('consumo_watts')->default(120);
            $table->unsignedInteger('costo_kwh')->default(50);
            $table->unsignedInteger('desgaste_maquina')->default(120);
            $table->unsignedInteger('costo_mano_obra')->default(0);
            $table->decimal('horas_impresion', 8, 2)->default(0);
            $table->unsignedInteger('tiempo_minutos')->default(0);
            $table->decimal('horas_trabajo', 8, 2)->default(0);
            $table->unsignedInteger('extras')->default(0);
            $table->unsignedInteger('margen_ganancia')->default(4);
            $table->unsignedInteger('cantidad_piezas')->default(1);
            $table->unsignedInteger('precio_venta')->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();
        });

        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->cascadeOnDelete();
            $table->date('fecha');
            $table->string('estado')->default('presupuesto');
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('descuento')->default(0);
            $table->string('tipo_venta')->default('contado');
            $table->string('factura_tipo')->nullable();
            $table->string('factura_numero')->nullable();
            $table->string('factura_cae')->nullable();
            $table->timestamps();
        });

        Schema::create('venta_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('articulo_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('cantidad')->default(1);
            $table->unsignedInteger('precio_unitario')->default(0);
            $table->unsignedInteger('subtotal')->default(0);
            $table->timestamps();
        });

        Schema::create('movimientos_cuenta_corriente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venta_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tipo');
            $table->integer('monto');
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->unsignedInteger('valor')->default(0);
            $table->string('texto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
        Schema::dropIfExists('movimientos_cuenta_corriente');
        Schema::dropIfExists('venta_items');
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('articulos');
        Schema::dropIfExists('clientes');
    }
};
