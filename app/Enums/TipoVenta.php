<?php

namespace App\Enums;

enum TipoVenta: string
{
    case Contado = 'contado';
    case Transferencia = 'transferencia';
    case CuentaCorriente = 'cuenta_corriente';
    case MercadoPago = 'mercado_pago';

    public function label(): string
    {
        return match ($this) {
            self::Contado => 'Contado',
            self::Transferencia => 'Transferencia',
            self::CuentaCorriente => 'Cuenta Corriente',
            self::MercadoPago => 'MercadoPago',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Contado => 'success',
            self::Transferencia => 'info',
            self::CuentaCorriente => 'warning',
            self::MercadoPago => 'primary',
        };
    }
}
