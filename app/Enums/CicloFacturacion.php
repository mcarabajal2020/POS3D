<?php

namespace App\Enums;

enum CicloFacturacion: string
{
    case Mensual = 'mensual';
    case Anual = 'anual';

    public function label(): string
    {
        return match ($this) {
            self::Mensual => 'Mensual',
            self::Anual => 'Anual',
        };
    }
}
