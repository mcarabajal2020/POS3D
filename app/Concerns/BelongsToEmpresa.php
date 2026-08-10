<?php

namespace App\Concerns;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToEmpresa
{
    public static function bootBelongsToEmpresa(): void
    {
        static::creating(function (Model $model) {
            if (is_null($model->empresa_id) && session('empresa_id')) {
                $model->empresa_id = session('empresa_id');
            }
        });
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function scopeDeEmpresa(Builder $query, ?int $empresaId = null): Builder
    {
        $empresaId = $empresaId ?? session('empresa_id');

        if (is_null($empresaId)) {
            return $query;
        }

        return $query->where('empresa_id', $empresaId);
    }
}
