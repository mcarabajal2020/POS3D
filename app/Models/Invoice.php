<?php

namespace App\Models;

use App\Enums\EstadoInvoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'subscription_id',
        'empresa_id',
        'monto',
        'estado',
        'fecha_emision',
        'fecha_vencimiento',
        'fecha_pago',
        'metodo_pago',
        'nota',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'integer',
            'estado' => EstadoInvoice::class,
            'fecha_emision' => 'date',
            'fecha_vencimiento' => 'date',
            'fecha_pago' => 'date',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function getFormattedMontoAttribute(): string
    {
        return '$ '.number_format($this->monto, 0, ',', '.');
    }
}
