<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\MovimientoCuentaCorriente;

class CuentaCorrienteService
{
    public function registrarPago(Cliente $cliente, int $monto, ?string $descripcion = null): MovimientoCuentaCorriente
    {
        $movimiento = MovimientoCuentaCorriente::create([
            'cliente_id' => $cliente->id,
            'venta_id' => null,
            'tipo' => 'pago',
            'monto' => -$monto,
            'descripcion' => $descripcion ?? 'Pago recibido',
        ]);

        $cliente->decrement('saldo', $monto);

        return $movimiento;
    }

    public function historial(Cliente $cliente)
    {
        return $cliente->movimientos()->latest()->get();
    }
}
