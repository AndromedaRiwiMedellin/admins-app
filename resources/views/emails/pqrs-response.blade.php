<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuesta a tu solicitud</title>
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
                        <p style="margin:6px 0 0;font-size:12px;color:rgba(225,255,187,0.7);">Respuesta a tu solicitud</p>
                    </td>
                </tr>

                {{-- Contenido --}}
                <tr>
                    <td style="padding:32px;">
                        <p style="margin:0 0 8px;font-size:14px;color:#6b7280;">
                            Hola, <strong style="color:#001A6E;">{{ $pqrs->user?->full_name ?? $pqrs->user?->email }}</strong>
                        </p>
                        <p style="margin:0 0 24px;font-size:14px;color:#6b7280;line-height:1.6;">
                            Hemos respondido tu solicitud. A continuación encontrarás los detalles.
                        </p>

                        {{-- Solicitud original --}}
                        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:20px;margin-bottom:16px;">
                            <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;">Tu solicitud</p>
                            <p style="margin:0 0 4px;font-size:14px;font-weight:600;color:#001A6E;">{{ $pqrs->subject }}</p>
                            <p style="margin:0;font-size:13px;color:#6b7280;line-height:1.5;">{{ $pqrs->message }}</p>
                        </div>

                        {{-- Respuesta --}}
                        <div style="background:#f0fdf9;border:1px solid #d1fae5;border-left:4px solid #009990;border-radius:10px;padding:20px;margin-bottom:24px;">
                            <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#009990;text-transform:uppercase;letter-spacing:0.5px;">Nuestra respuesta</p>
                            <p style="margin:0;font-size:14px;color:#374151;line-height:1.6;">{{ $response }}</p>
                        </div>

                        {{-- Estado --}}
                        @php
                            $statusLabels = ['pending'=>'Pendiente','in_process'=>'En gestión','resolved'=>'Resuelto'];
                            $statusColors = ['pending'=>'#f59e0b','in_process'=>'#3b82f6','resolved'=>'#009990'];
                        @endphp
                        <p style="margin:0 0 24px;font-size:13px;color:#6b7280;">
                            Estado actual:
                            <strong style="color:{{ $statusColors[$pqrs->status] ?? '#6b7280' }};">
                                {{ $statusLabels[$pqrs->status] ?? $pqrs->status }}
                            </strong>
                        </p>

                        <div style="text-align:center;">
                            <a href="{{ config('app.url') }}"
                               style="display:inline-block;background:#009990;color:#E1FFBB;text-decoration:none;font-size:14px;font-weight:600;padding:12px 28px;border-radius:8px;">
                                Ver mi solicitud
                            </a>
                        </div>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding:20px 32px;border-top:1px solid #f3f4f6;text-align:center;">
                        <p style="margin:0;font-size:11px;color:#d1d5db;">
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