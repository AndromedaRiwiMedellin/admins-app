<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de evento</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:'DM Sans',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:40px 20px;">
    <tr>
        <td align="center">
            <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;">

                {{-- Header --}}
                <tr>
                    <td style="background:#009990;padding:32px;text-align:center;">
                        <p style="margin:0;font-size:22px;font-weight:700;color:#E1FFBB;letter-spacing:-0.5px;">Orbix Teatro</p>
                        <p style="margin:6px 0 0;font-size:12px;color:rgba(225,255,187,0.7);">Actualización de evento</p>
                    </td>
                </tr>

                {{-- Contenido --}}
                <tr>
                    <td style="padding:32px;">
                        <p style="margin:0 0 8px;font-size:14px;color:#6b7280;">Hola, <strong style="color:#001A6E;">{{ $user->full_name ?? $user->email }}</strong></p>
                        <p style="margin:0 0 24px;font-size:14px;color:#6b7280;line-height:1.6;">
                            Te informamos que el evento que tienes marcado como favorito ha sido actualizado.
                        </p>

                        {{-- Card del evento --}}
                        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-left:4px solid #009990;border-radius:10px;padding:20px;margin-bottom:24px;">
                            <p style="margin:0 0 4px;font-size:16px;font-weight:600;color:#001A6E;">{{ $event->title }}</p>
                            @if($event->event_date)
                                <p style="margin:0 0 4px;font-size:13px;color:#6b7280;">
                                    📅 {{ \Carbon\Carbon::parse($event->event_date)->format('d \d\e F \d\e Y, h:i A') }}
                                </p>
                            @endif
                            @if($event->sale_start && $event->sale_end)
                                <p style="margin:0;font-size:13px;color:#6b7280;">
                                    🎟️ Venta: {{ \Carbon\Carbon::parse($event->sale_start)->format('d M Y') }}
                                    — {{ \Carbon\Carbon::parse($event->sale_end)->format('d M Y') }}
                                </p>
                            @endif
                        </div>

                        <p style="margin:0 0 24px;font-size:13px;color:#9ca3af;line-height:1.6;">
                            Ingresa a tu cuenta para ver todos los detalles actualizados y descargar tu entrada.
                        </p>

                        <div style="text-align:center;">
                            <a href="{{ config('app.url') }}"
                               style="display:inline-block;background:#009990;color:#E1FFBB;text-decoration:none;font-size:14px;font-weight:600;padding:12px 28px;border-radius:8px;">
                                Ver evento
                            </a>
                        </div>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding:20px 32px;border-top:1px solid #f3f4f6;text-align:center;">
                        <p style="margin:0;font-size:11px;color:#d1d5db;">
                            Recibiste este correo porque tienes este evento en favoritos.<br>
                            © {{ date('Y') }} Orbix Teatro — Todos los derechos reservados.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>