<?php

namespace App\Models;

use App\Concerns\BelongsToEmpresa;
use App\Enums\EstadoVenta;
use App\Enums\TipoComprobante;
use App\Enums\TipoVenta;
use Database\Factories\VentaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'cliente_id',
    'fecha',
    'estado',
    'total',
    'descuento',
    'tipo_venta',
    'factura_tipo',
    'factura_numero',
    'factura_cae',
])]
class Venta extends Model
{
    /** @use HasFactory<VentaFactory> */
    use BelongsToEmpresa, HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['estado', 'total', 'descuento', 'tipo_venta'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'estado' => EstadoVenta::class,
            'total' => 'integer',
            'descuento' => 'integer',
            'tipo_venta' => TipoVenta::class,
            'factura_tipo' => TipoComprobante::class,
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(VentaItem::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoCuentaCorriente::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '$ '.number_format($this->total, 0, ',', '.');
    }

    public function getFormattedDescuentoAttribute(): string
    {
        return '$ '.number_format($this->descuento, 0, ',', '.');
    }
}
