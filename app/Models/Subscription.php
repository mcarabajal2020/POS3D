<?php

namespace App\Models;

use App\Enums\CicloFacturacion;
use App\Enums\EstadoSubscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'empresa_id',
        'plan_id',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'trial_fin',
        'facturacion_ciclo',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoSubscription::class,
            'facturacion_ciclo' => CicloFacturacion::class,
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'trial_fin' => 'date',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', EstadoSubscription::Activa);
    }

    public function scopeTrial($query)
    {
        return $query->where('estado', EstadoSubscription::Trial);
    }

    public function estaActiva(): bool
    {
        return $this->estado === EstadoSubscription::Activa
            || $this->estado === EstadoSubscription::Trial;
    }

    public function estaVencida(): bool
    {
        if ($this->estado === EstadoSubscription::Cancelada) {
            return false;
        }

        if ($this->estado === EstadoSubscription::Trial && $this->trial_fin) {
            return $this->trial_fin->isPast();
        }

        if ($this->fecha_fin) {
            return $this->fecha_fin->isPast();
        }

        return false;
    }

    public function diasRestantes(): ?int
    {
        if ($this->estado === EstadoSubscription::Trial && $this->trial_fin) {
            return max(0, (int) now()->diffInDays($this->trial_fin, false));
        }

        if ($this->fecha_fin) {
            return max(0, (int) now()->diffInDays($this->fecha_fin, false));
        }

        return null;
    }
}
