<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComprobantePago extends Model
{
    protected $fillable = [
        'empresa_id',
        'subscription_id',
        'archivo',
        'monto',
        'estado',
        'notas',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }
}
