<?php

namespace App\Enums;

enum EstadoInvoice: string
{
    case Pendiente = 'pendiente';
    case Pagada = 'pagada';
    case Vencida = 'vencida';
    case Cancelada = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::Pagada => 'Pagada',
            self::Vencida => 'Vencida',
            self::Cancelada => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pendiente => 'warning',
            self::Pagada => 'success',
            self::Vencida => 'danger',
            self::Cancelada => 'gray',
        };
    }
}
