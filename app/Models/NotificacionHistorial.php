<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificacionHistorial extends Model
{
    protected $fillable = [
        'titulo',
        'mensaje',
        'tipo',
        'destino',
        'empresas_ids',
        'cantidad_usuarios',
        'enviada_por',
    ];

    protected $casts = [
        'empresas_ids' => 'array',
    ];

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enviada_por');
    }
}
