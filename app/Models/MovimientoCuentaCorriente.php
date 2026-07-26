<?php

namespace App\Models;

use Database\Factories\MovimientoCuentaCorrienteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['cliente_id', 'venta_id', 'tipo', 'monto', 'descripcion'])]
class MovimientoCuentaCorriente extends Model
{
    /** @use HasFactory<MovimientoCuentaCorrienteFactory> */
    use HasFactory;

    protected $table = 'movimientos_cuenta_corriente';

    protected function casts(): array
    {
        return [
            'monto' => 'integer',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function getFormattedMontoAttribute(): string
    {
        return ($this->monto >= 0 ? '+' : '-') . '$ ' . number_format(abs($this->monto), 0, ',', '.');
    }
}
