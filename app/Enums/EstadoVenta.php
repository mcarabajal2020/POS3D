<?php

namespace App\Enums;

enum EstadoVenta: string
{
    case Presupuesto = 'presupuesto';
    case Pendiente = 'pendiente';
    case EnProduccion = 'en_produccion';
    case Terminado = 'terminado';
    case Entregado = 'entregado';
    case Facturado = 'facturado';

    public function label(): string
    {
        return match ($this) {
            self::Presupuesto => 'Presupuesto',
            self::Pendiente => 'Pendiente',
            self::EnProduccion => 'En Producción',
            self::Terminado => 'Terminado',
            self::Entregado => 'Entregado',
            self::Facturado => 'Facturado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Presupuesto => 'gray',
            self::Pendiente => 'warning',
            self::EnProduccion => 'info',
            self::Terminado => 'success',
            self::Entregado => 'success',
            self::Facturado => 'primary',
        };
    }
}
