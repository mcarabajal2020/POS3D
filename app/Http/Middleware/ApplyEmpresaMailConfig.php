<?php

namespace App\Http\Middleware;

use App\Models\Configuracion;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyEmpresaMailConfig
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $mailer = Configuracion::get('mail_mailer', 'smtp');

            config([
                'mail.default' => $mailer,
                'mail.mailers.smtp.host' => Configuracion::get('mail_host', 'smtp.gmail.com'),
                'mail.mailers.smtp.port' => Configuracion::get('mail_port', '587'),
                'mail.mailers.smtp.username' => Configuracion::get('mail_username', ''),
                'mail.mailers.smtp.password' => Configuracion::get('mail_password', ''),
                'mail.mailers.smtp.encryption' => Configuracion::get('mail_encryption', 'tls'),
                'mail.from.address' => Configuracion::get('mail_from_address', ''),
                'mail.from.name' => Configuracion::get('mail_from_name', config('app.name')),
            ]);
        }

        return $next($request);
    }
}
