<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        Configuracion::set('costo_filamento_kg', 25000);

        Configuracion::setTexto('mail_mailer', 'smtp');
        Configuracion::setTexto('mail_host', 'smtp.gmail.com');
        Configuracion::setTexto('mail_port', '587');
        Configuracion::setTexto('mail_username', '');
        Configuracion::setTexto('mail_password', '');
        Configuracion::setTexto('mail_encryption', 'tls');
        Configuracion::setTexto('mail_from_address', 'no-reply@techxpress.com');
        Configuracion::setTexto('mail_from_name', 'TechXpress');
    }
}
