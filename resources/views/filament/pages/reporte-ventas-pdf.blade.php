<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #f59e0b; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin: 0; }
        .header p { color: #666; margin: 4px 0 0; }
        .summary { margin-bottom: 20px; }
        .summary h2 { font-size: 14px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .summary-grid { display: flex; flex-wrap: wrap; gap: 10px; }
        .summary-card { border: 1px solid #ddd; border-radius: 6px; padding: 10px; width: 22%; }
        .summary-card .label { font-size: 10px; color: #888; }
        .summary-card .value { font-size: 16px; font-weight: bold; margin-top: 2px; }
        .summary-card .sub { font-size: 9px; color: #aaa; }
        .detail { margin-top: 15px; }
        .detail h2 { font-size: 14px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f5f5f5; text-align: left; padding: 6px 8px; font-size: 10px; border-bottom: 2px solid #ddd; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-venta { background: #dbeafe; color: #1d4ed8; }
        .badge-pago { background: #d1fae5; color: #059669; }
        .text-venta { color: #1d4ed8; }
        .text-pago { color: #059669; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #aaa; border-top: 1px solid #eee; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Ventas y Cobros</h1>
        <p>Desde {{ $desde->format('d/m/Y') }} hasta {{ $hasta->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <h2>Resumen</h2>
        <div class="summary-grid">
            <div class="summary-card">
                <div class="label">Total Ventas</div>
                <div class="value">${{ number_format($datos['totales']['total_ventas'], 0, ',', '.') }}</div>
                <div class="sub">{{ $datos['totales']['cantidad_ventas'] }} ventas · Promedio ${{ number_format($datos['totales']['promedio'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Total Cobrado</div>
                <div class="value" style="color: #059669;">${{ number_format($datos['totales']['total_cobrado'], 0, ',', '.') }}</div>
                <div class="sub">Pagos recibidos</div>
            </div>
            <div class="summary-card">
                <div class="label">Descuentos</div>
                <div class="value" style="color: #d97706;">${{ number_format($datos['totales']['total_descuentos'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Cta. Corriente (pend.)</div>
                <div class="value" style="color: #dc2626;">${{ number_format($datos['totales']['total_cuenta_corriente'], 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="summary-grid" style="margin-top: 10px;">
            <div class="summary-card" style="width: 22%;">
                <div class="label">Contado</div>
                <div class="value" style="font-size: 13px;">${{ number_format($datos['totales']['total_contado'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-card" style="width: 22%;">
                <div class="label">Transferencia</div>
                <div class="value" style="font-size: 13px;">${{ number_format($datos['totales']['total_transferencia'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-card" style="width: 22%;">
                <div class="label">MercadoPago</div>
                <div class="value" style="font-size: 13px;">${{ number_format($datos['totales']['total_mercado_pago'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-card" style="width: 22%;">
                <div class="label">Cuenta Corriente</div>
                <div class="value" style="font-size: 13px;">${{ number_format($datos['totales']['total_cuenta_corriente'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="detail">
        <h2>Detalle de Movimientos</h2>
        @if($datos['timeline']->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Cliente</th>
                        <th>Descripción</th>
                        <th class="text-right">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos['timeline'] as $item)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($item['fecha'])->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $item['tipo'] === 'Venta' ? 'badge-venta' : 'badge-pago' }}">
                                    {{ $item['tipo'] }}
                                </span>
                            </td>
                            <td>{{ $item['cliente'] }}</td>
                            <td>{{ $item['descripcion'] }}</td>
                            <td class="text-right {{ $item['tipo'] === 'Venta' ? 'text-venta' : 'text-pago' }}">
                                ${{ number_format($item['monto'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-center" style="color: #999; padding: 20px;">No hay movimientos en el rango seleccionado.</p>
        @endif
    </div>

    <div class="footer">
        Documento generado automáticamente · {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
