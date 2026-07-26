<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Helvetica Neue', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background-color: #f59e0b; padding: 24px 30px;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: bold;">{{ config('app.name') }}</h1>
                            <p style="margin: 4px 0 0; color: rgba(255,255,255,0.85); font-size: 13px;">Impresión 3D y Prototipado</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            <p style="margin: 0 0 16px; color: #374151; font-size: 15px; line-height: 1.6;">
                                Hola <strong>{{ $venta->cliente->nombre }}</strong>,
                            </p>
                            <p style="margin: 0 0 16px; color: #374151; font-size: 15px; line-height: 1.6;">
                                Adjunto encontrás el comprobante de tu compra.
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0; border: 1px solid #e5e7eb; border-radius: 6px;">
                                <tr>
                                    <td style="padding: 16px; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="font-size: 13px; color: #6b7280; padding: 4px 0;">Comprobante</td>
                                                <td style="font-size: 13px; color: #111827; font-weight: bold; text-align: right; padding: 4px 0;">
                                                    {{ $venta->factura_tipo?->label() ?? 'Comprobante' }} #{{ $venta->id }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #6b7280; padding: 4px 0;">Fecha</td>
                                                <td style="font-size: 13px; color: #111827; text-align: right; padding: 4px 0;">{{ $venta->fecha->format('d/m/Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #6b7280; padding: 4px 0;">Tipo de venta</td>
                                                <td style="font-size: 13px; color: #111827; text-align: right; padding: 4px 0;">{{ $venta->tipo_venta?->label() }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 15px; color: #111827; font-weight: bold; padding: 12px 0 4px; border-top: 1px solid #e5e7eb; margin-top: 8px;">TOTAL</td>
                                                <td style="font-size: 18px; color: #f59e0b; font-weight: bold; text-align: right; padding: 12px 0 4px; border-top: 1px solid #e5e7eb;">
                                                    $ {{ number_format($venta->total, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 20px 0 0; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                Si tenés alguna consulta, no dudes en contactarnos.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f9fafb; padding: 16px 30px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #9ca3af; font-size: 11px; text-align: center;">
                                Este es un correo generado automáticamente por {{ config('app.name') }}.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
