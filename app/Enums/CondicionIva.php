<?php

namespace App\Enums;

enum CondicionIva: string
{
    case ResponsableInscripto = 'responsable_inscripto';
    case Monotributo = 'monotributo';
    case ConsumidorFinal = 'consumidor_final';
    case Exento = 'exento';
    case NoResponsable = 'no_responsable';

    public function label(): string
    {
        return match ($this) {
            self::ResponsableInscripto => 'Responsable Inscripto',
            self::Monotributo => 'Monotributo',
            self::ConsumidorFinal => 'Consumidor Final',
            self::Exento => 'Exento',
            self::NoResponsable => 'No Responsable',
        };
    }
}
