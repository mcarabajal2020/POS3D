# TechXpress - POS 3D

Sistema de punto de venta (POS) para negocios de impresión 3D, desarrollado con Laravel 13 y Filament v5.

## Funcionalidades

### Clientes
- ABM completo de clientes (alta, baja, modificación)
- Historial de movimientos y saldo de cuenta corriente
- Búsqueda y filtrado por condición IVA

### Artículos
- ABM de productos con precios de venta
- Calculadora de costos de producción:
  - Costo de material (filamento por kg)
  - Consumo eléctrico según modelo de impresora (A1 / A1 Mini / P1S / X1C)
  - Desgaste de máquina
  - Mano de obra
  - Márgenes de ganancia (x1 a x6)
  - Cantidad de piezas
- Guía rápida de consumo por modelo de impresora

### Ventas
- Registro de ventas con múltiples ítems
- Tipos de venta: Contado, Transferencia, Cuenta Corriente, MercadoPago
- Estados: Presupuesto, Pendiente, En Producción, Terminado, Entregado, Facturado
- Generación de comprobantes (Factura A/B/C, Presupuesto, Nota Crédito, Nota Débito)
- Descarga de comprobantes en PDF
- Envío de comprobantes por email con PDF adjunto
- Tabla ordenada por fecha descendente
- Descuento general y por ítem

### Cuenta Corriente
- Gestión de saldos por cliente
- Registro de pagos con aplicación automática a deuda
- Pago a venta específica o balance general
- Historial de movimientos (ventas y pagos)

### Cobros
- Registro de cobros contra cuenta corriente
- Resumen de deuda pendiente por cliente

### Comprobantes PDF
- Generación de comprobantes en formato PDF (Dompdf)
- Diseño table-based para compatibilidad con dompdf
- Datos del cliente, ítems, totales e IVA
- Adjunto automático en envío por email

### Configuración
- Costo del filamento por kg
- Configuración de correo SMTP:
  - Transporte (SMTP, Sendmail, Mailgun, SES, Postmark, Resend)
  - Host, puerto, usuario, contraseña, cifrado
  - Dirección y nombre del remitente
- Aplicación automática de configuración de correo al iniciar la app

### Panel de Estadísticas
- Widget de ventas del día (total, cobradas, pendientes)
- Conteo de ventas por estado
- Monto promedio de ventas

## Stack Tecnológico

- **Backend:** Laravel 13, PHP 8.4
- **Frontend:** Filament v5, Livewire 4, Alpine.js, Tailwind CSS
- **Base de datos:** SQLite (desarrollo) / MySQL / PostgreSQL
- **PDF:** barryvdh/laravel-dompdf
- **Moneda:** Pesos argentinos (ARS)

## Instalación

```bash
# Clonar el repositorio
git clone https://github.com/mcarabajal2020/POS3D.git
cd POS3D

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Base de datos
php artisan migrate --seed

# Compilar assets
npm run build

# Iniciar servidor
php artisan serve
```

## Credenciales por defecto

- **Email:** admin@admin.com
- **Contraseña:** password

## Licencia

MIT
