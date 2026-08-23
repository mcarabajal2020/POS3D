<?php

namespace App\Models;

use App\Concerns\BelongsToEmpresa;
use Database\Factories\FilamentoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'nombre',
    'precio_kg',
])]
class Filamento extends Model
{
    /** @use HasFactory<FilamentoFactory> */
    use BelongsToEmpresa, HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'precio_kg'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'precio_kg' => 'integer',
        ];
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(Articulo::class);
    }

    public function getFormattedPrecioKgAttribute(): string
    {
        return '$ '.number_format($this->precio_kg, 0, ',', '.');
    }
}
