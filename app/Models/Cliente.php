<?php

namespace App\Models;

use App\Enums\CondicionIva;
use Database\Factories\ClienteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'cuit_cuil', 'direccion', 'telefono', 'email', 'condicion_iva', 'saldo'])]
class Cliente extends Model
{
    /** @use HasFactory<ClienteFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'condicion_iva' => CondicionIva::class,
            'saldo' => 'integer',
        ];
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoCuentaCorriente::class);
    }

    public function routeNotificationForWhatsapp(): string
    {
        return $this->telefono;
    }

    public function getFormattedSaldoAttribute(): string
    {
        return ($this->saldo >= 0 ? '' : '-').'$ '.number_format(abs($this->saldo), 0, ',', '.');
    }
}
