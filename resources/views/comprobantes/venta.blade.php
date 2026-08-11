<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .page { width: 75%; margin: 0 auto; padding: 20px; }

        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 2px solid #f59e0b; padding-bottom: 15px; }
        .company-name { font-size: 22px; font-weight: bold; color: #f59e0b; }
        .company-info { font-size: 9px; color: #666; margin-top: 3px; line-height: 1.4; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 18px; color: #333; text-transform: uppercase; }
        .comprobante-tipo { font-size: 13px; color: #f59e0b; font-weight: bold; margin-top: 3px; }

        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details-table td { width: 50%; vertical-align: top; padding: 0 8px 0 0; }
        .details-table td:last-child { padding: 0 0 0 8px; }
        .box-title { font-size: 9px; text-transform: uppercase; color: #999; letter-spacing: 1px; margin-bottom: 6px; border-bottom: 1px solid #eee; padding-bottom: 3px; }
        .box-content { font-size: 10px; line-height: 1.7; }
        .label { color: #999; }

        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items thead th { background: #f59e0b; color: #fff; padding: 7px 6px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
        table.items thead th.r { text-align: right; }
        table.items tbody td { padding: 7px 6px; border-bottom: 1px solid #eee; font-size: 10px; }
        table.items tbody td.r { text-align: right; }
        table.items tbody tr:nth-child(even) { background: #fafafa; }

        .totals-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .totals-table td { padding: 4px 0; font-size: 10px; }
        .totals-table .label-col { text-align: right; padding-right: 8px; }
        .totals-table .value-col { text-align: right; width: 120px; }
        .totals-table .subtotal-row td { border-bottom: 1px solid #eee; padding-bottom: 6px; }
        .totals-table .descuento-row td { color: #dc2626; }
        .totals-table .total-row td { border-top: 2px solid #f59e0b; font-size: 13px; font-weight: bold; padding-top: 8px; }

        .footer { margin-top: 30px; border-top: 1px solid #eee; padding-top: 12px; font-size: 9px; color: #999; text-align: center; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="page">
        <table class="header-table">
            <tr>
                <td style="vertical-align: top;">
                    <div class="company-name">{{ $venta->empresa->nombre ?? config('app.name') }}</div>
                    <div class="company-info">
                        {{ $venta->empresa->direccion ?? 'Buenos Aires, Argentina' }}<br>
                        @if($venta->empresa->email)
                            {{ $venta->empresa->email }}<br>
                        @endif
                        @if($venta->empresa->telefono)
                            Tel: {{ $venta->empresa->telefono }}
                        @endif
                    </div>
                </td>
                <td class="invoice-title" style="vertical-align: top;">
                    <h1>Comprobante</h1>
                    @if($venta->factura_tipo)
                        <div class="comprobante-tipo">{{ $venta->factura_tipo->label() }}</div>
                    @endif
                    @if($venta->factura_numero)
                        <div style="font-size: 11px; color: #666; margin-top: 3px;">N° {{ $venta->factura_numero }}</div>
                    @endif
                </td>
            </tr>
        </table>

        <table class="details-table">
            <tr>
                <td>
                    <div class="box-title">Cliente</div>
                    <div class="box-content">
                        <strong>{{ $venta->cliente->nombre }}</strong><br>
                        @if($venta->cliente->cuit_cuil)
                            CUIT/CUIL: {{ $venta->cliente->cuit_cuil }}<br>
                        @endif
                        @if($venta->cliente->condicion_iva)
                            {{ $venta->cliente->condicion_iva->label() }}<br>
                        @endif
                        @if($venta->cliente->direccion)
                            {{ $venta->cliente->direccion }}<br>
                        @endif
                        @if($venta->cliente->telefono)
                            Tel: {{ $venta->cliente->telefono }}<br>
                        @endif
                        @if($venta->cliente->email)
                            {{ $venta->cliente->email }}
                        @endif
                    </div>
                </td>
                <td>
                    <div class="box-title">Detalles del comprobante</div>
                    <div class="box-content">
                        <span class="label">Fecha:</span> {{ $venta->fecha->format('d/m/Y') }}<br>
                        <span class="label">Estado:</span> {{ $venta->estado?->label() }}<br>
                        <span class="label">Tipo de venta:</span> {{ $venta->tipo_venta?->label() }}<br>
                        @if($venta->factura_cae)
                            <span class="label">CAE:</span> {{ $venta->factura_cae }}<br>
                        @endif
                        <span class="label">Venta #:</span> {{ $venta->id }}
                    </div>
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th>Artículo</th>
                    <th class="r" style="width: 45px;">Cant.</th>
                    <th class="r" style="width: 85px;">Precio Unit.</th>
                    <th class="r" style="width: 90px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->articulo->nombre ?? 'N/A' }}</td>
                        <td class="r">{{ $item->cantidad }}</td>
                        <td class="r">$ {{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                        <td class="r">$ {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr class="subtotal-row">
                <td class="label-col">Subtotal</td>
                <td class="value-col">$ {{ number_format($venta->items->sum('subtotal'), 0, ',', '.') }}</td>
            </tr>
            @if($venta->descuento > 0)
                <tr class="descuento-row">
                    <td class="label-col">Descuento</td>
                    <td class="value-col">- $ {{ number_format($venta->descuento, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td class="label-col">TOTAL</td>
                <td class="value-col">$ {{ number_format($venta->total, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Gracias por su compra</p>
            <p>Documento generado automáticamente por {{ $venta->empresa->nombre ?? config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
