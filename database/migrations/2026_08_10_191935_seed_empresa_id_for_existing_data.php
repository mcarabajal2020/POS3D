<?php

use App\Models\Empresa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $empresa = Empresa::firstOrCreate(
            ['nombre' => 'Empresa Principal'],
            [
                'cuit' => '20-00000000-0',
                'activa' => true,
            ]
        );

        $tables = ['clientes', 'articulos', 'ventas', 'venta_items', 'movimientos_cuenta_corriente', 'configuraciones'];

        foreach ($tables as $table) {
            DB::table($table)->whereNull('empresa_id')->update(['empresa_id' => $empresa->id]);
        }

        DB::table('user_empresa')->insert([
            'user_id' => 1,
            'empresa_id' => $empresa->id,
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $tables = ['clientes', 'articulos', 'ventas', 'venta_items', 'movimientos_cuenta_corriente', 'configuraciones'];

        foreach ($tables as $table) {
            DB::table($table)->update(['empresa_id' => null]);
        }

        DB::table('user_empresa')->truncate();
    }
};
