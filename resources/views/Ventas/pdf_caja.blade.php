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
                        <span class="bold text-red">CANCELADO</span>
                    @else
                        @php
                            $comentario = '';
                            if(isset($v->comentarios)) {
                                $comentario = $v->comentarios;
                            } else {
                                $comentario = \Illuminate\Support\Facades\DB::table('Venta')->where('id_venta', $v->id_venta ?? $v->id ?? 0)->value('comentarios') ?? '';
                            }

                            $comentarioUpper = strtoupper($comentario);
                            $esCortesia100 = str_contains($comentarioUpper, 'CORTESÍA 100%') || str_contains($comentarioUpper, 'CORTESIA 100%') || str_contains($comentarioUpper, 'DESCUENTO 100%');
                            $esCortesia40 = str_contains($comentarioUpper, 'CORTESÍA 40%') || str_contains($comentarioUpper, 'CORTESIA 40%') || str_contains($comentarioUpper, 'DESCUENTO 40%');
                            
                            $pagos_texto = $v->montos_detalle ?? ($v->metodos ?? '');

                            // AGREGAMOS EL NOMBRE DEL CLIENTE BLINDADO CONTRA ERRORES
                            $nClie = '';
                            if (isset($v->tipo_servicio)) {
                                if ($v->tipo_servicio == 1 || $v->tipo_servicio == 3) {
                                    $nClie = ' - ' . $v->nombreClie;
                                }
                            } else {
                                // Si por algo no viene "tipo_servicio", comprobamos con el nombre
                                if (isset($v->nombreClie) && $v->nombreClie !== 'PARA LLEVAR') {
                                    $nClie = ' - ' . $v->nombreClie;
                                }
                            }
                        @endphp

                        @if($esCortesia100)
                            <span style="background-color: #fee2e2; color: #991b1b; padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 11px; border: 1px solid #f87171; display: inline-block; margin-bottom: 4px;">DESCUENTO 100%{{ mb_strtoupper($nClie) }}</span><br>
                        @endif

                        @if($esCortesia40)
                            <span style="background-color: #fef3c7; color: #b45309; padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 11px; border: 1px solid #fbbf24; display: inline-block; margin-bottom: 4px;">DESCUENTO 40%{{ mb_strtoupper($nClie) }}</span><br>
                        @endif

                        @if(empty(trim($pagos_texto)) && !$esCortesia100)
                            <span style="color: #64748b; font-weight: bold; font-size: 11px;">PENDIENTE</span>
                        @elseif(!empty(trim($pagos_texto)))
                            @php
                                $pagos_array = explode('<br>', $pagos_texto);
                                $pagos_formateados = [];
                                foreach($pagos_array as $pago) {
                                    $pago_limpio = trim($pago);
                                    if(empty($pago_limpio)) continue;
                                    
                                    $pagoUpper = mb_strtoupper($pago_limpio);
                                    if (str_contains($pagoUpper, 'EFECTIVO')) {
                                        // Verde para Efectivo
                                        $pagos_formateados[] = '<span style="color: #16a34a; font-weight: bold;">' . $pago_limpio . '</span>';
                                    } elseif (str_contains($pagoUpper, 'TARJETA')) {
                                        // Morado para Tarjeta
                                        $pagos_formateados[] = '<span style="color: #9333ea; font-weight: bold;">' . $pago_limpio . '</span>';
                                    } elseif (str_contains($pagoUpper, 'TRANSFERENCIA')) {
                                        // Azul para Transferencia
                                        $pagos_formateados[] = '<span style="color: #2563eb; font-weight: bold;">' . $pago_limpio . '</span>';
                                    } else {
                                        $pagos_formateados[] = '<span>' . $pago_limpio . '</span>';
                                    }
                                }
                                $pagos_html = implode('<br>', $pagos_formateados);
                            @endphp
                            <div style="margin-top: 3px;">{!! $pagos_html !!}</div>
                        @endif
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