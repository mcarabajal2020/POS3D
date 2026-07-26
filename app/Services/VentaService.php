<?php

namespace App\Services;

use App\Enums\EstadoVenta;
use App\Enums\TipoVenta;
use App\Models\MovimientoCuentaCorriente;
use App\Models\Venta;

class VentaService
{
    public function calcularTotal(Venta $venta): int
    {
        $subtotal = $venta->items->sum('subtotal');

        return max(0, $subtotal - $venta->descuento);
    }

    public function recalcularTotal(Venta $venta): void
    {
        $venta->update(['total' => $this->calcularTotal($venta)]);
    }

    public function registrarEnCuentaCorriente(Venta $venta): void
    {
        if ($venta->tipo_venta !== TipoVenta::CuentaCorriente) {
            return;
        }

        $cliente = $venta->cliente;

        MovimientoCuentaCorriente::create([
            'cliente_id' => $cliente->id,
            'venta_id' => $venta->id,
            'tipo' => 'venta',
            'monto' => $venta->total,
            'descripcion' => "Venta #{$venta->id}",
        ]);

        $cliente->increment('saldo', $venta->total);
    }

    public function totalDelDia(?TipoVenta $tipo = null): int
    {
        $query = Venta::whereDate('fecha', today());

        if ($tipo) {
            $query->where('tipo_venta', $tipo);
        }

        return $query->sum('total');
    }

    public function totalContadoDelDia(): int
    {
        return $this->totalDelDia(TipoVenta::Contado);
    }

    public function totalTransferenciasDelDia(): int
    {
        return $this->totalDelDia(TipoVenta::Transferencia);
    }

    public function puedeTransicionar(Venta $venta, EstadoVenta $nuevoEstado): bool
    {
        $transiciones = [
            EstadoVenta::Presupuesto => [EstadoVenta::Pendiente, EstadoVenta::Facturado],
            EstadoVenta::Pendiente => [EstadoVenta::EnProduccion, EstadoVenta::Facturado],
            EstadoVenta::EnProduccion => [EstadoVenta::Terminado],
            EstadoVenta::Terminado => [EstadoVenta::Entregado],
            EstadoVenta::Entregado => [EstadoVenta::Facturado],
            EstadoVenta::Facturado => [],
        ];

        return in_array($nuevoEstado, $transiciones[$venta->estado] ?? []);
    }
}
