<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropColumn([
                'costo_por_gramo',
                'costo_hora_maquina',
                'energia_electrica',
                'post_procesado',
            ]);
        });

        Schema::table('articulos', function (Blueprint $table) {
            $table->string('tipo_material')->default('PLA')->after('descripcion');
            $table->integer('consumo_watts')->default(120)->after('tipo_material');
            $table->unsignedInteger('costo_kwh')->default(50)->after('consumo_watts');
            $table->unsignedInteger('desgaste_maquina')->default(120)->after('costo_kwh');
            $table->unsignedInteger('costo_mano_obra')->default(0)->after('desgaste_maquina');
            $table->decimal('horas_trabajo', 8, 2)->default(0)->after('costo_mano_obra');
            $table->unsignedInteger('extras')->default(0)->after('horas_trabajo');
            $table->unsignedInteger('margen_ganancia')->default(4)->after('extras');
            $table->unsignedInteger('cantidad_piezas')->default(1)->after('margen_ganancia');
            $table->unsignedInteger('tiempo_minutos')->default(0)->after('horas_impresion');
        });
    }

    public function down(): void
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_material',
                'consumo_watts',
                'costo_kwh',
                'desgaste_maquina',
                'costo_mano_obra',
                'horas_trabajo',
                'extras',
                'margen_ganancia',
                'cantidad_piezas',
                'tiempo_minutos',
            ]);
        });

        Schema::table('articulos', function (Blueprint $table) {
            $table->unsignedInteger('costo_por_gramo')->default(0);
            $table->unsignedInteger('costo_hora_maquina')->default(0);
            $table->unsignedInteger('energia_electrica')->default(0);
            $table->unsignedInteger('post_procesado')->default(0);
        });
    }
};
