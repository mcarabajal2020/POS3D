<?php

namespace App\Models;

use App\Concerns\BelongsToEmpresa;
use App\Services\CostoProduccionService;
use Database\Factories\ArticuloFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'codigo_sku',
    'nombre',
    'descripcion',
    'filamento_id',
    'impresora_id',
    'tipo_material',
    'filamento_gramos',
    'horas_impresion',
    'tiempo_minutos',
    'consumo_watts',
    'costo_kwh',
    'desgaste_maquina',
    'costo_mano_obra',
    'horas_trabajo',
    'extras',
    'margen_ganancia',
    'cantidad_piezas',
    'precio_venta',
    'stock',
])]
class Articulo extends Model
{
    /** @use HasFactory<ArticuloFactory> */
    use BelongsToEmpresa, HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'precio_venta', 'stock', 'codigo_sku'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'filamento_gramos' => 'decimal:2',
            'horas_impresion' => 'decimal:2',
            'tiempo_minutos' => 'integer',
            'consumo_watts' => 'integer',
            'costo_kwh' => 'integer',
            'desgaste_maquina' => 'decimal:2',
            'costo_mano_obra' => 'integer',
            'horas_trabajo' => 'decimal:2',
            'extras' => 'integer',
            'margen_ganancia' => 'integer',
            'cantidad_piezas' => 'integer',
            'precio_venta' => 'integer',
            'stock' => 'integer',
        ];
    }

    public function ventaItems(): HasMany
    {
        return $this->hasMany(VentaItem::class);
    }

    public function filamento(): BelongsTo
    {
        return $this->belongsTo(Filamento::class);
    }

    public function impresora(): BelongsTo
    {
        return $this->belongsTo(Impresora::class);
    }

    public function costoProduccion(): int
    {
        return (int) app(CostoProduccionService::class)->calcular($this)['costo_total'];
    }

    public function costoDesglose(): array
    {
        return app(CostoProduccionService::class)->calcular($this);
    }

    public function getFormattedPrecioAttribute(): string
    {
        return '$ '.number_format($this->precio_venta, 0, ',', '.');
    }

    public function getFormattedCostoProduccionAttribute(): string
    {
        return '$ '.number_format($this->costoProduccion(), 0, ',', '.');
    }
}
