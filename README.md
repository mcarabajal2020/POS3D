# TechXpress - POS 3D

Sistema de punto de venta (POS) para negocios de impresión 3D, desarrollado con Laravel 13, Filament v5 y arquitectura multi-empresa (SaaS-ready).

## Funcionalidades

### Multi-Empresa
- Arquitectura multi-empresa con aislamiento de datos por sesión
- Cada empresa tiene sus propios clientes, artículos, ventas y configuración
- Selección de empresa al iniciar sesión
- Cliente "Consumidor Final" auto-creado por empresa

### Roles y Permisos
- Sistema de roles con Spatie Permission + Filament Shield
- Roles predefinidos: Super Admin, Admin, Vendedor, Cajero
- Panel Super Admin separado para gestión global de usuarios y notificaciones

### Clientes
- ABM completo de clientes (alta, baja, modificación)
- Condiciones IVA: Consumidor Final, Monotributo, Responsable Inscripto, Exento
- CUIT/Cuil único por empresa
- Historial de movimientos y saldo de cuenta corriente

### Artículos
- ABM de productos con cálculo automático de SKU
- Calculadora de costos de producción:
  - Costo de material (filamento por gramos + precio/kg)
  - Consumo eléctrico (watts × horas × costo kWh)
  - Desgaste de máquina (por hora)
  - Mano de obra
  - Extras
  - Márgenes de ganancia (x1 a x6)
  - Cantidad de piezas
- Formato monetario argentino con máscara (1.500, 12.500, etc.)
- Stock actual por artículo

### Filamentos
- ABM de filamentos con precio por kg
- Tipos: PLA, ABS, PETG, TPU
- Asociación a artículos para cálculo de costos

### Impresoras
- ABM de impresoras
- Consumo en watts, desgaste por hora
- Modelos: A1, A1 Mini, P1S, X1C

### Ventas
- Registro de ventas con múltiples ítems
- Tipos de venta: Contado, Transferencia, Cuenta Corriente, MercadoPago
- Estados: Presupuesto, Pendiente, En Producción, Terminado, Entregado, Facturado
- Cambio de estado con notificación por email
- Generación de comprobantes (Factura A/B/C, Presupuesto, Nota Crédito, Nota Débito)
- Descarga de comprobantes en PDF
- Envío de comprobantes por email con PDF adjunto
- Descuento general y por ítem

### Cuenta Corriente
- Gestión de saldos por cliente
- Registro de pagos con aplicación automática a deuda
- Pago a venta específica o balance general
- Historial de movimientos (ventas y pagos)
- Saldos a favor distinguidos de deudas

### Cobros
- Registro de cobros contra cuenta corriente
- Resumen de deuda pendiente por cliente

### Comprobantes PDF
- Generación de comprobantes en formato PDF (Dompdf)
- Datos del cliente, ítems, totales e IVA
- Adjunto automático en envío por email

### Reportes
- Reporte de ventas por rango de fechas
- Desglose por tipo de pago (contado, transferencia, cuenta corriente, MercadoPago)
- Total cobrado con convención contable explícita
- Exportación a CSV (números raw para compatibilidad con Excel)
- Exportación a PDF
- Timeline de movimientos (ventas + pagos)

### Dashboard
- Ventas del mes, cobradas y pendientes
- Gráfico de ventas diarias (Timeline)
- Gráfico de facturación acumulada
- Gráfico de deuda vs saldo a favor (3 datasets: facturado, deuda, saldo a favor)
- Conteo de ventas por estado

### Notificaciones (Super Admin)
- Envío de notificaciones a todos los usuarios, solo super admins, o por empresa(s)
- Historial de notificaciones enviadas
- Notificaciones nativas de Filament (campana)

### Auditoría (Activity Log)
- Registro de cambios en: Ventas, Artículos, Filamentos, Impresoras, VentaItems, MovimientosCuentaCorriente
- Log solo de campos modificados (`logOnlyDirty`)
- Modelo: quién, qué, cuándo, antes, después

### Configuración
- Costo del filamento por kg
- Configuración de correo SMTP por empresa (cifrado con Encripted Cast)
- Aplicación automática de configuración de correo al iniciar la app

## Stack Tecnológico

- **Backend:** Laravel 13, PHP 8.4
- **Frontend:** Filament v5, Livewire 4, Alpine.js, Tailwind CSS
- **Base de datos:** SQLite
- **PDF:** barryvdh/laravel-dompdf
- **Auditoría:** spatie/laravel-activitylog
- **Permisos:** spatie/laravel-permission + filament-shield
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

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Compilar assets
npm run build

# Iniciar servidor
php artisan serve
```

## Credenciales por defecto

- **Super Admin:** maxi@admin.com / password
- **Admin:** elias@admin.com / password

## Licencia

MIT
