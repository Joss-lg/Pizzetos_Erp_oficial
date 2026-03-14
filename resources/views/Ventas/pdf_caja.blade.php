<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cierre de Caja - Pizzetos</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', sans-serif; color: #1e293b; line-height: 1.4; margin: 0; padding: 0; font-size: 10px; }
        
        /* Encabezado Estilo Pizzetos */
        .header { background-color: #f8fafc; padding: 20px; border-bottom: 3px solid #f59e0b; margin-bottom: 20px; }
        .header table { width: 100%; }
        .brand-container { vertical-align: middle; }
        .logo { height: 50px; width: auto; }
        .report-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #64748b; letter-spacing: 2px; margin-top: 5px; }
        
        /* Bloques de Información */
        .section-title { font-size: 11px; font-weight: 900; text-transform: uppercase; font-style: italic; color: #000; border-left: 4px solid #f59e0b; padding-left: 8px; margin: 20px 0 10px 0; }
        
        /* Grid de Resumen Financiero */
        .kpi-container { width: 100%; margin-bottom: 20px; }
        .kpi-box { width: 23%; padding: 10px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; display: inline-block; vertical-align: top; margin-right: 1%; text-align: center; }
        .kpi-title { font-size: 7px; font-weight: bold; text-transform: uppercase; color: #94a3b8; margin-bottom: 5px; }
        .kpi-value { font-size: 12px; font-weight: 900; color: #1e293b; font-style: italic; }
        .bg-amber-light { background-color: #fef3c7; border-color: #f59e0b; }
        .text-green { color: #166534; }
        .text-red { color: #991b1b; }

        /* Tablas de Datos */
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th { background-color: #f8fafc; padding: 8px; text-align: left; font-size: 8px; font-weight: bold; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        table.data-table td { padding: 8px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        
        /* Footer */
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; padding: 10px 0; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td class="brand-container">
                    <img src="{{ public_path('pizzetos.png') }}" class="logo">
                    {{-- MODIFICADO: USANDO FOLIO VIRTUAL DE CAJA --}}
                    <div class="report-title">Acta de Cierre de Caja #{{ $caja->folio_virtual }}</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div style="font-weight: bold; font-size: 8px; color: #94a3b8;">FECHA DE EMISIÓN</div>
                    <div style="font-weight: 900; font-style: italic;">{{ date('d/m/Y H:i:s') }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- 1. INFORMACIÓN DE OPERACIÓN --}}
    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="width: 50%; border:none;">
                <div class="section-title">Datos de Apertura</div>
                <div style="margin-left: 10px;">
                    <div><b>Iniciado por:</b> {{ $caja->responsable_apertura ?? 'Admin' }}</div>
                    <div><b>Fecha/Hora:</b> {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y h:i a') }}</div>
                    <div><b>Comentarios:</b> {{ $caja->observaciones_apertura ?? 'Sin notas' }}</div>
                </div>
            </td>
            <td style="width: 50%; border:none;">
                <div class="section-title">Datos de Cierre</div>
                <div style="margin-left: 10px;">
                    <div><b>Finalizado el:</b> {{ $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y h:i a') : 'OPERACIÓN EN CURSO' }}</div>
                    <div><b>Efectivo contado:</b> ${{ number_format($caja->monto_final ?? 0, 2) }}</div>
                    <div><b>Notas de cierre:</b> {{ $caja->observaciones_cierre ?? 'Sin observaciones' }}</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- 2. INDICADORES FINANCIEROS --}}
    <div class="section-title">Resumen Financiero</div>
    <div class="kpi-container">
        <div class="kpi-box bg-amber-light">
            <div class="kpi-title">Fondo Inicial</div>
            <div class="kpi-value">${{ number_format($stats['fondo'], 2) }}</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-title">Venta Bruta (Total)</div>
            <div class="kpi-value">${{ number_format($stats['venta_total'], 2) }}</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-title">Egresos (Gastos)</div>
            <div class="kpi-value text-red">-${{ number_format($stats['total_gastos'], 2) }}</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-title">Efectivo Real (Sistema)</div>
            <div class="kpi-value text-green">${{ number_format($stats['arqueo_real'] ?? $stats['efectivo_esperado'], 2) }}</div>
        </div>
    </div>

    {{-- 3. CONCILIACIÓN POR MÉTODO --}}
    <div class="section-title">Conciliación por Métodos de Pago</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Método de Pago</th>
                <th>Venta Bruta</th>
                <th>Egresos Aplicados</th>
                <th style="text-align: right;">Monto Neto en Caja</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><b>EFECTIVO (VENTAS)</b></td>
                <td>${{ number_format($stats['efectivo'], 2) }}</td>
                <td class="text-red">-${{ number_format($stats['total_gastos'], 2) }}</td>
                <td style="text-align: right;" class="text-green"><b>${{ number_format($stats['efectivo_esperado'], 2) }}</b></td>
            </tr>
            <tr>
                <td><b>TARJETA (VOUCHERS BANCARIOS)</b></td>
                <td>${{ number_format($stats['tarjeta'], 2) }}</td>
                <td>$0.00</td>
                <td style="text-align: right;">${{ number_format($stats['tarjeta'], 2) }}</td>
            </tr>
            <tr>
                <td><b>TRANSFERENCIAS ELECTRÓNICAS</b></td>
                <td>${{ number_format($stats['transferencia'], 2) }}</td>
                <td>$0.00</td>
                <td style="text-align: right;">${{ number_format($stats['transferencia'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- 4. DETALLE DE GASTOS --}}
    <div class="section-title">Detalle de Gastos (Egresos)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="60%">Descripción / Concepto</th>
                <th width="20%">Responsable</th>
                <th width="20%" style="text-align: right;">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gastos as $g)
            <tr>
                <td>{{ $g->descripcion }}</td>
                <td>{{ $g->responsable }}</td>
                <td style="text-align: right;" class="text-red">-${{ number_format($g->precio, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; color: #94a3b8;">No hay egresos registrados en este turno.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 5. HISTORIAL DE PEDIDOS --}}
    <div class="section-title">Historial de Pedidos Procesados</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="15%">Folio</th>
                <th width="32%">Cliente / Servicio</th>
                <th width="33%">Pagos / Referencias</th>
                <th width="20%" style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $v)
            <tr>
                {{-- MODIFICADO: USANDO FOLIO VIRTUAL DE PEDIDO --}}
                <td><b>#{{ $v->folio_virtual }}</b></td>
                <td>{{ $v->nombreClie }}</td>
                <td>
                    {{ $v->metodos }} 
                    @if($v->refs && $v->refs != '-') <br><span style="font-size: 7px; color: #64748b;">Ref: {{ $v->refs }}</span> @endif
                </td>
                <td style="text-align: right;"><b>${{ number_format($v->total, 2) }}</b></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; padding: 10px; font-weight: 900; font-style: italic;">Diferencia de Arqueo (Sistema vs Físico):</td>
                <td style="text-align: right; padding: 10px; font-weight: 900;" class="{{ ($stats['diferencia'] ?? 0) < 0 ? 'text-red' : 'text-green' }}">
                    ${{ number_format($stats['diferencia'] ?? 0, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Este documento es un comprobante oficial de Pizzetos. Generado por Ollintem ERP.<br>
        © {{ date('Y') }} Pizzetos - Todos los derechos reservados.
    </div>

</body>
</html>