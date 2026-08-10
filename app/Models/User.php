<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'user_empresa')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function empresaActual(): ?Empresa
    {
        return session('empresa_id') ? Empresa::find(session('empresa_id')) : $this->empresas()->first();
    }

    public function roleEnEmpresa(Empresa $empresa): ?string
    {
        return $this->empresas()->where('empresa_id', $empresa->id)->first()?->pivot->role;
    }

    public function isAdmin(): bool
    {
        $empresa = $this->empresaActual();

        if (! $empresa) {
            return false;
        }

        return $this->roleEnEmpresa($empresa) === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->id === 1;
    }
}
