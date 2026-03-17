<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 0; }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 13px; 
            margin: 0 auto; 
            padding: 10px; 
            width: 230px; 
            color: #000; 
            text-transform: uppercase; 
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-xl { font-size: 22px; letter-spacing: 1px; }
        .text-lg { font-size: 16px; }
        .mt-1 { margin-top: 5px; }
        .mb-1 { margin-bottom: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { text-align: left; vertical-align: top; padding: 3px 0; }
        th { border-bottom: 1px dashed #000; font-weight: bold; padding-bottom: 3px; font-size: 12px;}
        
        .sub-item { font-size: 12px; color: #333; }
        .sub-text { padding-left: 8px; }
        
        .flex-between { display: flex; justify-content: space-between; align-items: center; }
        
        .ticket-logo {
            width: 150px;
            height: auto;
            margin-bottom: 5px;
            filter: grayscale(100%) contrast(1.2);
        }

        @media print {
            body { padding: 0; width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="text-center mb-1">
        {{-- LOGO DE LA PIZZERÍA --}}
        <img src="{{ asset('pizzetos.png') }}" alt="Pizzetos Logo" class="ticket-logo">
        
        <div style="font-size: 12px;">TICKET DE VENTA</div>
        
        <div class="font-bold mt-1" style="font-size: 16px;">
            FOLIO: {{ str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) }}
        </div>
        
        <div style="font-size: 12px;">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y h:i A') }}</div>
        
        <div class="font-bold text-lg mt-1 mb-1 py-1" style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 5px 0;">
            @if($venta->tipo_servicio == 1)
                * COMEDOR - MESA {{ $venta->mesa }} *
            @elseif($venta->tipo_servicio == 2)
                * PARA LLEVAR *
            @elseif($venta->tipo_servicio == 3)
                * SERVICIO A DOMICILIO *
            @endif
        </div>
    </div>

    {{-- DATOS DEL CLIENTE --}}
    @if($venta->tipo_servicio == 3 && $domicilio)
        <div class="mb-1" style="font-size: 12px;">
            <div class="font-bold" style="font-size: 14px;">CLIENTE:</div>
            <div>{{ trim(($domicilio->cnombre ?? '') . ' ' . ($domicilio->capellido ?? '')) }}</div>
            <div><span class="font-bold">TEL:</span> {{ $domicilio->telefono ?? 'S/N' }}</div>
            <div class="mt-1"><span class="font-bold">DIR:</span> {{ $domicilio->calle ?? 'S/N' }}</div>
            <div><span class="font-bold">COL:</span> {{ $domicilio->colonia ?? 'S/N' }}</div>
            <div><span class="font-bold">MZ:</span> {{ $domicilio->manzana ?? '-' }} | <span class="font-bold">LT:</span> {{ $domicilio->lote ?? '-' }}</div>
            @if(isset($domicilio->referencia) && $domicilio->referencia)
                <div><span class="font-bold">REF:</span> {{ $domicilio->referencia }}</div>
            @endif
        </div>
        <div style="border-top: 1px dashed #000; margin-top: 5px; margin-bottom: 5px;"></div>
    @elseif($venta->tipo_servicio == 1 && $venta->nombreClie)
        <div class="mb-1" style="font-size: 12px;">
            <div class="font-bold">CLIENTE:</div>
            <div>{{ $venta->nombreClie }}</div>
        </div>
        <div style="border-top: 1px dashed #000; margin-top: 5px; margin-bottom: 5px;"></div>
    @endif

    <table class="mb-1">
        <thead>
            <tr>
                <th style="width: 15%;">CANT</th>
                <th style="width: 60%;">DESCRIPCIÓN</th>
                <th style="width: 25%; text-align: right;">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($final_items as $item)
                <tr>
                    {{-- Si no hay cantidad (como en pizzas agrupadas), no poner la X --}}
                    <td class="font-bold" style="white-space: nowrap;">{{ $item->cantidad }}</td>
                    <td class="font-bold">{{ $item->nombre }}</td>
                    <td class="text-right font-bold">${{ number_format($item->total, 2) }}</td>
                </tr>
                
                @foreach($item->subs as $sub)
                <tr class="sub-item">
                    <td></td>
                    {{-- Usamos font-bold y nos aseguramos de que cada sub-elemento sea una fila nueva --}}
                    <td class="font-bold" colspan="2" style="padding-left: 5px;">{{ $sub }}</td>
                </tr>
                @endforeach
                
                {{-- Espacio separador entre productos --}}
                <tr><td colspan="3" style="height: 8px; border-bottom: 1px dashed #eee;"></td></tr>
            @endforeach
        </tbody>
    </table>

    {{-- SECCIÓN DE COMENTARIOS --}}
    @if($venta->comentarios)
        <div style="border-top: 1px dashed #000; margin-top: 5px;"></div>
        
        <div class="text-center" style="padding: 5px 0; font-size: 12px; font-weight: bold;">
            {{ $venta->comentarios }}
        </div>
        
    @endif

    {{-- ZONA DE TOTAL Y PAGOS --}}
    @if($venta->status == 0)
        <div class="text-center font-bold" style="border: 2px solid #000; padding: 5px; margin-top: 10px;">
            CUENTA ABIERTA<br>PENDIENTE DE PAGO
        </div>
    @else
        
        {{-- TOTAL A PAGAR  --}}
        <div style="border-top: 1px dashed #000; margin-top: 5px;"></div>
        
        <div style="padding: 5px 0;">
            <div class="flex-between">
                <div class="font-bold text-lg" style="margin: 0;">TOTAL A PAGAR:</div>
                <div class="font-bold text-lg" style="margin: 0;">${{ number_format($venta->total, 2) }}</div>
            </div>
        </div>

        <div style="border-top: 1px dashed #000; margin-bottom: 10px;"></div>

        {{-- MÉTODOS DE PAGO LIBRES POR DEBAJO --}}
        <div>
            <div class="font-bold" style="font-size: 13px; margin-bottom: 5px;">MÉTODO DE PAGO:</div>
            
            @foreach($pagos as $pago)
                <div style="margin-bottom: 4px;">
                    @if($pago->id_metpago == 1)
                        <div class="flex-between font-bold">
                            <span>TARJETA</span>
                            <span>${{ number_format($pago->monto, 2) }}</span>
                        </div>
                    @elseif($pago->id_metpago == 2)
                        <div class="flex-between font-bold">
                            <span>EFECTIVO</span>
                            <span>${{ number_format($pago->monto, 2) }}</span>
                        </div>
                        @if($pago->referencia && is_numeric($pago->referencia) && $pago->referencia > $pago->monto)
                            <div class="flex-between" style="font-size: 11px; color: #333;">
                                <span>RECIBIDO:</span>
                                <span>${{ number_format($pago->referencia, 2) }}</span>
                            </div>
                            <div class="flex-between" style="font-size: 11px; color: #333;">
                                <span>CAMBIO:</span>
                                <span>${{ number_format($pago->referencia - $pago->monto, 2) }}</span>
                            </div>
                        @endif
                    @elseif($pago->id_metpago == 3)
                        <div class="flex-between font-bold">
                            <span>TRANSFERENCIA</span>
                            <span>${{ number_format($pago->monto, 2) }}</span>
                        </div>
                        @if($pago->referencia)
                            <div style="font-size: 11px; color: #333;">REF: {{ $pago->referencia }}</div>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>

    @endif

    <div class="text-center mt-1 pt-1" style="margin-top: 20px; font-size: 12px; font-weight: bold;">
        ¡GRACIAS POR SU PREFERENCIA!
    </div>

    {{-- LÓGICA DE AUTO-IMPRESIÓN Y AUTO-CIERRE DE POPUP --}}
    <script>
        window.onload = function() {
            window.print();
        };

        window.onafterprint = function() {
            window.close();
        };
    </script>

</body>
</html>