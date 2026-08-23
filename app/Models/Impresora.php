<?php

namespace App\Models;

use App\Concerns\BelongsToEmpresa;
use Database\Factories\ImpresoraFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'nombre',
    'consumo_watts',
    'desgaste_hora',
])]
class Impresora extends Model
{
    /** @use HasFactory<ImpresoraFactory> */
    use BelongsToEmpresa, HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'consumo_watts', 'desgaste_hora'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'consumo_watts' => 'integer',
            'desgaste_hora' => 'integer',
        ];
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(Articulo::class);
    }

    public function getLabelAttribute(): string
    {
        return "{$this->nombre} ({$this->consumo_watts}W | Desgaste: \${$this->desgaste_hora}/h)";
    }
}
