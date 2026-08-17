<?php

namespace App\Models;

use App\Concerns\BelongsToEmpresa;
use Database\Factories\VentaItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['venta_id', 'articulo_id', 'cantidad', 'precio_unitario', 'subtotal'])]
class VentaItem extends Model
{
    /** @use HasFactory<VentaItemFactory> */
    use BelongsToEmpresa, HasFactory;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (VentaItem $item) {
            if (is_null($item->subtotal) && $item->cantidad && $item->precio_unitario) {
                $item->subtotal = $item->cantidad * $item->precio_unitario;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'precio_unitario' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function articulo(): BelongsTo
    {
        return $this->belongsTo(Articulo::class);
    }

    public function getFormattedPrecioUnitarioAttribute(): string
    {
        return '$ '.number_format($this->precio_unitario, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return '$ '.number_format($this->subtotal, 0, ',', '.');
    }
}
