<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cierre de Caja #{{ $caja->id_caja }}</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; color: #1e293b; margin: 0; padding: 10px; line-height: 1.4; }
        .header { border-bottom: 3px solid #2563eb; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { margin: 0; color: #0f172a; font-size: 26px; }
        .header p { margin: 4px 0; color: #64748b; font-size: 12px; }
        
        .section-title { background: #f1f5f9; padding: 10px 15px; font-weight: bold; border-radius: 6px; margin-bottom: 15px; font-size: 14px; text-transform: uppercase; border-left: 4px solid #2563eb; }
        
        .row { width: 100%; margin-bottom: 20px; display: block; clear: both; }
        .kpi-box { float: left; width: 23%; background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; text-align: center; margin-right: 1.5%; box-sizing: border-box; }
        .kpi-box:last-child { margin-right: 0; }
        
        .kpi-title { font-size: 9px; color: #94a3b8; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; letter-spacing: 0.5px;}
        .kpi-value { font-size: 16px; font-weight: 900; color: #0f172a; margin: 0; }
        .text-red { color: #dc2626 !important; }

        .payment-box { float: left; width: 32%; background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin-right: 2%; box-sizing: border-box;}
        .payment-box:last-child { margin-right: 0; }
        .payment-title { font-size: 13px; font-weight: bold; margin-bottom: 2px; }
        .payment-pct { font-size: 10px; color: #94a3b8; margin-bottom: 6px; }
        .payment-value { font-size: 16px; font-weight: 900; margin: 0; }
        
        .summary-box { background: #fef9c3; border: 2px solid #fde047; border-radius: 8px; padding: 20px; margin-top: 30px; clear: both; }
        .summary-row { width: 100%; margin-bottom: 8px; font-size: 13px; clear: both; }
        .summary-label { float: left; width: 70%; color: #475569; }
        .summary-val { float: right; width: 28%; text-align: right; font-weight: 900; color: #0f172a; }
        
        .total-line { border-top: 1px solid #eab308; padding-top: 10px; margin-top: 10px; }
        .footer { text-align: center; margin-top: 50px; font-size: 9px; color: #cbd5e1; border-top: 1px solid #f1f5f9; padding-top: 10px; }
        
        /* Clearfix para los floats */
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Reporte de Cierre de Caja #{{ $caja->id_caja }}</h1>
        <p><strong>Sucursal:</strong> {{ auth()->user()->id_suc ?? 'Matriz' }}</p>
        <p><strong>Cajero:</strong> {{ $caja->cajero_nombre ?? 'Admin' }}</p>
        <p><strong>Apertura:</strong> {{ \Carbon\Carbon::parse($caja->fecha_apertura)->locale('es')->isoFormat('LLLL') }}</p>
        <p><strong>Cierre:</strong> {{ $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre)->locale('es')->isoFormat('LLLL') : 'Caja abierta' }}</p>
    </div>

    <div class="section-title">Resumen de Operación</div>
    <div class="row clearfix">
        <div class="kpi-box">
            <div class="kpi-title">Fondo Inicial</div>
            <div class="kpi-value">${{ number_format($caja->monto_inicial, 2) }}</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-title">Venta Bruta</div>
            <div class="kpi-value">${{ number_format($stats['venta_total'], 2) }}</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-title">Tickets</div>
            <div class="kpi-value">{{ $stats['num_ventas'] }}</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-title text-red">Gastos</div>
            <div class="kpi-value text-red">-${{ number_format($stats['total_gastos'], 2) }}</div>
        </div>
    </div>

    @php
        $totalPagos = $stats['efectivo'] + $stats['tarjeta'] + $stats['transferencia'];
        $pctEfe = $totalPagos > 0 ? round(($stats['efectivo'] / $totalPagos) * 100, 1) : 0;
        $pctTar = $totalPagos > 0 ? round(($stats['tarjeta'] / $totalPagos) * 100, 1) : 0;
        $pctTra = $totalPagos > 0 ? round(($stats['transferencia'] / $totalPagos) * 100, 1) : 0;
        
        $efectivoEsperado = ($caja->monto_inicial + $stats['efectivo']) - $stats['total_gastos'];
    @endphp

    <div class="section-title">Desglose de Ingresos</div>
    <div class="row clearfix">
        <div class="payment-box">
            <div class="payment-title">Efectivo</div>
            <div class="payment-pct">{{ $pctEfe }}% de ingresos</div>
            <div class="payment-value">${{ number_format($stats['efectivo'], 2) }}</div>
        </div>
        <div class="payment-box">
            <div class="payment-title">Tarjeta</div>
            <div class="payment-pct">{{ $pctTar }}% de ingresos</div>
            <div class="payment-value">${{ number_format($stats['tarjeta'], 2) }}</div>
        </div>
        <div class="payment-box">
            <div class="payment-title">Transferencia</div>
            <div class="payment-pct">{{ $pctTra }}% de ingresos</div>
            <div class="payment-value">${{ number_format($stats['transferencia'], 2) }}</div>
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-row">
            <div class="summary-label">Venta Bruta Total:</div>
            <div class="summary-val">${{ number_format($stats['venta_total'], 2) }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label text-red">Total Gastos de Turno:</div>
            <div class="summary-val text-red">-${{ number_format($stats['total_gastos'], 2) }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Ingresos No Efectivo (Tarjeta/Trans):</div>
            <div class="summary-val">-${{ number_format($stats['tarjeta'] + $stats['transferencia'], 2) }}</div>
        </div>
        <div class="summary-row total-line">
            <div class="summary-label" style="font-weight: bold; color: #0f172a; font-size: 14px;">EFECTIVO TOTAL ESPERADO:</div>
            <div class="summary-val" style="font-size: 18px; color: #15803d;">${{ number_format($efectivoEsperado, 2) }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Efectivo Reportado por Cajero:</div>
            <div class="summary-val">${{ number_format($caja->monto_final, 2) }}</div>
        </div>
        <div class="summary-row total-line">
            <div class="summary-label" style="font-weight: bold;">Diferencia (Sobrante/Faltante):</div>
            <div class="summary-val" style="color: {{ ($caja->monto_final - $efectivoEsperado) < 0 ? '#dc2626' : '#15803d' }}">
                ${{ number_format($caja->monto_final - $efectivoEsperado, 2) }}
            </div>
        </div>
    </div>

    @if($caja->observaciones_cierre)
    <div style="margin-top: 20px; font-size: 11px;">
        <strong>Observaciones de cierre:</strong><br>
        {{ $caja->observaciones_cierre }}
    </div>
    @endif

    <div class="footer">
        Pizzetos POS v2.0 - Reporte generado por {{ auth()->user()->nickName ?? 'Sistema' }} el {{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }}
    </div>
</body>
</html>