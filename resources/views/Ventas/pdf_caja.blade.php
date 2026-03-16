<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cierre de Caja - Pizzetos</title>
    <style>
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 13px; 
            color: #333; 
            margin: 0; 
            padding: 0; 
        }
        
        /* Control de saltos de página */
        .page-break { page-break-before: always; }
        
        /* Utilidades de texto */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .text-red { color: #dc2626; }
        .line-through { text-decoration: line-through; }
        
        /* Elementos gráficos solicitados */
        .linea-azul { 
            border-top: 3px solid #2563eb; 
            margin: 20px 0; 
        }
        
        .cuadro-amarillo {
            background-color: #fef08a; /* Amarillo suave */
            border: 2px solid #eab308; /* Borde amarillo fuerte */
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }

        /* Tablas */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-bordered th, .table-bordered td { 
            border: 1px solid #cbd5e1; 
            padding: 10px; 
        }
        .table-bordered th { 
            background-color: #f8fafc; 
            text-transform: uppercase; 
            text-align: left;
        }
        .table-layout td { border: none; padding: 5px 0; }
        
        .section-title { font-size: 18px; margin-bottom: 5px; color: #000; }
    </style>
</head>
<body>

    {{-- ================= HOJA 1: RESUMEN GENERAL ================= --}}
    
    <div class="text-center">
        <img src="{{ public_path('pizzetos.png') }}" style="max-width: 150px;">
    </div>
    
    <h1 class="text-center" style="font-size: 24px; margin-top: 10px;">CIERRE DE CAJA #{{ $caja->folio_virtual }}</h1>
    
    <table class="table-layout">
        <tr>
            <td class="bold">Apertura:</td>
            <td>{{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y h:i A') }}</td>
            <td class="text-right bold">Cierre:</td>
            <td class="text-right">{{ $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y h:i A') : 'EN CURSO' }}</td>
        </tr>
        <tr>
            <td class="bold">Cajero:</td>
            <td colspan="3">{{ $caja->responsable_apertura ?? 'ADMIN' }}</td>
        </tr>
    </table>

    <div class="linea-azul"></div>

    <h2 class="section-title">Información General</h2>
    <table class="table-bordered">
        <tr><td style="width: 50%;">Fondo Inicial</td><td class="text-right">$ {{ number_format($stats['fondo'], 2) }}</td></tr>
        <tr><td>Venta Total</td><td class="text-right">$ {{ number_format($stats['venta_total'], 2) }}</td></tr>
        <tr><td>Número de Pedidos</td><td class="text-right">{{ $stats['num_ventas'] }}</td></tr>
        <tr><td>Gastos</td><td class="text-right text-red">-$ {{ number_format($stats['total_gastos'], 2) }}</td></tr>
    </table>

    <h2 class="section-title" style="margin-top: 25px;">Desglose por Método de Pago</h2>
    <table class="table-bordered">
        <tr><td style="width: 50%;">Efectivo</td><td class="text-right">$ {{ number_format($stats['efectivo'], 2) }}</td></tr>
        <tr><td>Tarjeta</td><td class="text-right">$ {{ number_format($stats['tarjeta'], 2) }}</td></tr>
        <tr><td>Transferencia</td><td class="text-right">$ {{ number_format($stats['transferencia'], 2) }}</td></tr>
    </table>

    {{-- CUADRO AMARILLO --}}
    <div class="cuadro-amarillo">
        <h3 class="text-center" style="margin-top: 0; font-size: 18px;">RESUMEN FINAL DE CAJA</h3>
        <table class="table-layout">
            <tr><td class="bold">Venta Total:</td><td class="text-right">$ {{ number_format($stats['venta_total'], 2) }}</td></tr>
            <tr><td class="bold">Gastos:</td><td class="text-right text-red">-$ {{ number_format($stats['total_gastos'], 2) }}</td></tr>
            <tr><td class="bold">Tarjeta:</td><td class="text-right">$ {{ number_format($stats['tarjeta'], 2) }}</td></tr>
            <tr><td class="bold">Transferencia:</td><td class="text-right">$ {{ number_format($stats['transferencia'], 2) }}</td></tr>
            <tr>
                <td class="bold" style="font-size: 16px; padding-top: 15px;">Efectivo Esperado (Caja):</td>
                {{-- Efectivo de ventas MENOS Gastos (Ignoramos el fondo inicial) --}}
                <td class="text-right bold" style="font-size: 16px; padding-top: 15px;">$ {{ number_format($stats['efectivo'] - $stats['total_gastos'], 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- ================= HOJA 2: DETALLES DE GASTOS ================= --}}
    <div class="page-break"></div>
    
    <h2 class="text-center" style="font-size: 20px;">DETALLES DE GASTOS - CAJA #{{ $caja->folio_virtual }}</h2>
    
    <table class="table-bordered">
        <thead>
            <tr>
                <th style="width: 50%;">Concepto</th>
                <th style="width: 30%;">Responsable</th>
                <th style="width: 20%;" class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gastos as $g)
            <tr>
                <td>{{ $g->descripcion }}</td>
                <td class="text-center">{{ $g->responsable }}</td>
                <td class="text-right text-red">-$ {{ number_format($g->precio, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center" style="color: #666;">No se registraron gastos en este turno.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ================= HOJA 3: DETALLES DE VENTAS ================= --}}
    <div class="page-break"></div>
    
    <h2 class="text-center" style="font-size: 20px;">DETALLES DE VENTAS - CAJA #{{ $caja->folio_virtual }}</h2>
    
    <table class="table-bordered">
        <thead>
            <tr>
                <th style="width: 15%;">Id Venta</th>
                <th style="width: 25%;">Referencia</th>
                <th style="width: 45%;">Desglose por Método de Pago</th>
                <th style="width: 15%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $v)
            <tr class="{{ isset($v->status) && $v->status == 3 ? 'text-red line-through' : '' }}">
                <td class="text-center bold">#{{ $v->folio_virtual }}</td>
                <td class="text-center">{{ $v->refs && $v->refs != '-' ? $v->refs : 'N/A' }}</td>
                <td>
                    @if(isset($v->status) && $v->status == 3)
                        <span class="bold">CANCELADO</span>
                    @else
                        {!! $v->montos_detalle ?? $v->metodos !!}
                    @endif
                </td>
                <td class="text-right bold">$ {{ number_format($v->total, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center" style="color: #666;">No hay ventas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>