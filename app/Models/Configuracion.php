<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = ['clave', 'valor', 'texto'];

    public static function get(string $clave, int|string|null $default = null): int|string|null
    {
        $config = static::where('clave', $clave)->first();

        if (! $config) {
            return $default;
        }

        return $config->texto ?? $config->valor ?? $default;
    }

    public static function setInt(string $clave, int $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
    }

    public static function setTexto(string $clave, ?string $texto): void
    {
        static::updateOrCreate(['clave' => $clave], ['texto' => $texto]);
    }

    public static function set(string $clave, int|string|null $valor): void
    {
        if (is_int($valor)) {
            static::setInt($clave, $valor);
        } else {
            static::setTexto($clave, $valor);
        }
    }
}
