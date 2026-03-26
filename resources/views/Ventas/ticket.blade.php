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
        <img src="{{ asset('pizzetos.png') }}" alt="Pizzetos Logo" class="ticket-logo">
        
        <div style="font-size: 12px;">TICKET DE VENTA</div>
        
        <div class="font-bold mt-1" style="font-size: 16px;">
            FOLIO: {{ str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) }}
        </div>
        
        <div style="font-size: 12px;">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y h:i A') }}</div>
        
        <div class="font-bold text-lg mt-1 mb-1 py-1" style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 5px 0;">
            @if($venta->tipo_servicio == 1)
                MESA {{ $venta->mesa }}
            @elseif($venta->tipo_servicio == 2)
                PARA LLEVAR
            @elseif($venta->tipo_servicio == 3)
                DOMICILIO
            @endif
        </div>
    </div>

    @if($venta->tipo_servicio == 3 && $domicilio)
        <div class="mb-1" style="font-size: 11px; line-height: 1.4; margin-bottom: 5px;">
            <span class="font-bold" style="font-size: 13px;">CLIENTE:</span> {{ trim(($domicilio->cnombre ?? '') . ' ' . ($domicilio->capellido ?? '')) }} | 
            <span class="font-bold">TEL:</span> {{ $domicilio->telefono ?? 'S/N' }} | 
            <span class="font-bold">DIR:</span> {{ $domicilio->calle ?? 'S/N' }}, 
            <span class="font-bold">COL:</span> {{ $domicilio->colonia ?? 'S/N' }}, 
            <span class="font-bold">MZ:</span> {{ $domicilio->manzana ?? '-' }}, <span class="font-bold">LT:</span> {{ $domicilio->lote ?? '-' }}
            @if(isset($domicilio->referencia) && $domicilio->referencia)
                | <span class="font-bold">REF:</span> {{ $domicilio->referencia }}
            @endif
        </div>
        <div style="border-top: 1px dashed #000; margin-top: 5px; margin-bottom: 5px;"></div>
    @elseif(($venta->tipo_servicio == 2 || $venta->tipo_servicio == 1) && $venta->nombreClie)
        <div class="mb-1" style="font-size: 12px; line-height: 1.3;">
            <span class="font-bold">CLIENTE:</span> {{ mb_strtoupper($venta->nombreClie) }}
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
                <tr style="font-size: 16px; font-weight: 900; line-height: 1.1;">
                    <td style="white-space: nowrap; vertical-align: top; padding-top: 4px;">{{ $item->cantidad }}</td>
                    <td style="vertical-align: top; padding-top: 4px;">{{ $item->nombre }}</td>
                    <td class="text-right" style="vertical-align: top; padding-top: 4px; {{ str_contains($item->nombre, 'PAPAS') ? 'font-size: 15px;' : '' }}">
                        @if($item->total !== null)
                            ${{ number_format($item->total, 2) }}
                        @endif
                    </td>
                </tr>
                
                @foreach($item->subs as $sub)
                    <tr class="sub-item">
                        <td></td>
                        @if(is_array($sub))
                            <td style="font-size: 14px; font-weight: bold; padding-left: 5px; padding-bottom: 2px;">
                                {!! str_replace(' / ', ' <span style="font-weight: 900; font-size: 16px; margin: 0 3px;">/</span> ', e($sub['texto'])) !!}
                            </td>
                            <td class="text-right" style="font-size: 14px; font-weight: bold; padding-bottom: 2px;">
                                @if(isset($sub['precio']))
                                    ${{ number_format($sub['precio'], 2) }}
                                @elseif(isset($sub['precio_ext']) && $sub['precio_ext'] != '')
                                    {{ $sub['precio_ext'] }}
                                @endif
                            </td>
                        @else
                            <td colspan="2" style="font-size: 14px; font-weight: bold; padding-left: 5px; padding-bottom: 2px;">
                                {!! str_replace(' / ', ' <span style="font-weight: 900; font-size: 16px; margin: 0 3px;">/</span> ', e($sub)) !!}
                            </td>
                        @endif
                    </tr>
                @endforeach
                
                <tr><td colspan="3" style="height: 12px;"></td></tr>
            @endforeach
        </tbody>
    </table>

    @if($venta->comentarios)
        <div style="border-top: 1px dashed #000; margin-top: 5px;"></div>
        
        <div class="text-center" style="padding: 5px 0; font-size: 12px; font-weight: bold;">
            {{ $venta->comentarios }}
        </div>
    @endif

    @if($venta->status == 0)
        <div class="text-center font-bold" style="border: 2px solid #000; padding: 5px; margin-top: 10px;">
            CUENTA ABIERTA<br>PENDIENTE DE PAGO
        </div>
    @else
        <div style="border-top: 1px dashed #000; margin-top: 5px;"></div>
        
        <div style="padding: 5px 0;">
            <div class="flex-between">
                <div class="font-bold text-lg" style="margin: 0;">TOTAL A PAGAR:</div>
                <div class="font-bold text-lg" style="margin: 0;">${{ number_format($venta->total, 2) }}</div>
            </div>
        </div>

        <div style="border-top: 1px dashed #000; margin-bottom: 10px;"></div>

        <div>
            <div class="font-bold" style="font-size: 13px; margin-bottom: 5px;">MÉTODO DE PAGO:</div>
            
            @foreach($pagos as $pago)
                <div style="margin-bottom: 5px;">
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