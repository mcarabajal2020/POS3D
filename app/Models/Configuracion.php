<?php

namespace App\Models;

use App\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Configuracion extends Model
{
    use BelongsToEmpresa;

    protected $table = 'configuraciones';

    protected $fillable = ['clave', 'valor', 'texto'];

    private const ENCRYPTED_KEYS = [
        'mail_password',
        'mail_username',
    ];

    public static function get(string $clave, int|string|null $default = null): int|string|null
    {
        $config = static::deEmpresa()->where('clave', $clave)->first();

        if (! $config) {
            return $default;
        }

        $value = $config->texto ?? $config->valor ?? $default;

        if (in_array($clave, self::ENCRYPTED_KEYS) && $value !== null) {
            return self::decryptValue($value);
        }

        return $value;
    }

    public static function setInt(string $clave, int $valor): void
    {
        static::deEmpresa()->updateOrCreate(['clave' => $clave], ['valor' => $valor]);
    }

    public static function setTexto(string $clave, ?string $texto): void
    {
        if (in_array($clave, self::ENCRYPTED_KEYS) && $texto !== null) {
            $texto = self::encryptValue($texto);
        }

        static::deEmpresa()->updateOrCreate(['clave' => $clave], ['texto' => $texto]);
    }

    public static function set(string $clave, int|string|null $valor): void
    {
        if (is_int($valor)) {
            static::setInt($clave, $valor);
        } else {
            static::setTexto($clave, $valor);
        }
    }

    private static function encryptValue(string $value): string
    {
        if (self::isEncrypted($value)) {
            return $value;
        }

        return Crypt::encryptString($value);
    }

    private static function decryptValue(string $value): string
    {
        if (! self::isEncrypted($value)) {
            return $value;
        }

        return Crypt::decryptString($value);
    }

    private static function isEncrypted(string $value): bool
    {
        return str_starts_with($value, 'eyJ');
    }
}
