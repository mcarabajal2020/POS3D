<?php

namespace App\Enums;

enum TipoComprobante: string
{
    case FacturaA = 'factura_a';
    case FacturaB = 'factura_b';
    case FacturaC = 'factura_c';
    case Presupuesto = 'presupuesto';
    case NotaCredito = 'nota_credito';
    case NotaDebito = 'nota_debito';

    public function label(): string
    {
        return match ($this) {
            self::FacturaA => 'Factura A',
            self::FacturaB => 'Factura B',
            self::FacturaC => 'Factura C',
            self::Presupuesto => 'Presupuesto',
            self::NotaCredito => 'Nota de Crédito',
            self::NotaDebito => 'Nota de Débito',
        };
    }
}
