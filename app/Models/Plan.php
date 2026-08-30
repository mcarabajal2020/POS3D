<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'nombre',
        'precio_mensual',
        'precio_anual',
        'max_usuarios',
        'max_ventas_mensuales',
        'max_articulos',
        'max_filamentos',
        'max_impresoras',
        'trial_dias',
        'activo',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'precio_mensual' => 'integer',
            'precio_anual' => 'integer',
            'max_usuarios' => 'integer',
            'max_ventas_mensuales' => 'integer',
            'max_articulos' => 'integer',
            'max_filamentos' => 'integer',
            'max_impresoras' => 'integer',
            'trial_dias' => 'integer',
            'activo' => 'boolean',
            'orden' => 'integer',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function getFormattedPrecioMensualAttribute(): string
    {
        return '$ '.number_format($this->precio_mensual, 0, ',', '.');
    }

    public function getFormattedPrecioAnualAttribute(): ?string
    {
        return $this->precio_anual
            ? '$ '.number_format($this->precio_anual, 0, ',', '.')
            : null;
    }

    public function isEnterprise(): bool
    {
        return $this->max_usuarios === 0;
    }
}
