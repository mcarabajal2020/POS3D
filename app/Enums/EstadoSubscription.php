<?php

namespace App\Enums;

enum EstadoSubscription: string
{
    case Trial = 'trial';
    case Activa = 'activa';
    case Vencida = 'vencida';
    case Cancelada = 'cancelada';
    case Suspendida = 'suspendida';

    public function label(): string
    {
        return match ($this) {
            self::Trial => 'Prueba',
            self::Activa => 'Activa',
            self::Vencida => 'Vencida',
            self::Cancelada => 'Cancelada',
            self::Suspendida => 'Suspendida',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Trial => 'info',
            self::Activa => 'success',
            self::Vencida => 'danger',
            self::Cancelada => 'gray',
            self::Suspendida => 'warning',
        };
    }
}
