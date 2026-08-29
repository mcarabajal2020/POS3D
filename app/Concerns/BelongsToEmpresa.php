<?php

namespace App\Concerns;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Scope;

class EmpresaScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $empresaId = session('empresa_id');

        if (is_null($empresaId)) {
            return;
        }

        $builder->where("{$model->getTable()}.empresa_id", $empresaId);
    }

    public function extend(Builder $builder): void
    {
        $builder->macro('withoutEmpresaScope', function (Builder $builder) {
            return $builder->withoutGlobalScope(EmpresaScope::class);
        });
    }
}

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

    public static function bootedBelongsToEmpresa(): void
    {
        static::addGlobalScope(new EmpresaScope);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function scopeDeEmpresa(Builder $query, ?int $empresaId = null): Builder
    {
        $empresaId = $empresaId ?? session('empresa_id');

        if (is_null($empresaId)) {
            throw new \RuntimeException('Empresa no identificada. Por favor, seleccione una empresa.');
        }

        return $query->where("{$this->getTable()}.empresa_id", $empresaId);
    }
}
