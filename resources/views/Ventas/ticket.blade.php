<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $venta->id_venta }}</title>
    <style>
        @page { margin: 0; }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 13px; 
            margin: 0; 
            padding: 10px; 
            width: 280px; 
            color: #000; 
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-xl { font-size: 22px; }
        .text-lg { font-size: 16px; }
        .mt-1 { margin-top: 5px; }
        .mb-1 { margin-bottom: 5px; }
        .border-top { border-top: 1px solid #000; padding-top: 5px; margin-top: 5px; }
        .border-bottom { border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 5px; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; vertical-align: top; padding: 2px 0; }
        th { border-bottom: 1px dashed #000; font-weight: normal; }
        
        .sub-item { 
            font-size: 12px; 
            font-style: italic; 
            padding-left: 15px; 
            color: #333;
        }
        
        @media print {
            body { padding: 0; width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center mb-1">
        <div class="font-bold text-xl mb-1">PIZZETOS</div>
        <div>Ticket de Venta</div>
        <div class="font-bold">FOLIO: {{ $venta->id_venta }}</div>
        <div>{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('j/n/Y, g:i:s a') }}</div>
        <div class="font-bold text-lg mt-1 mb-1">
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
        <div class="border-top border-bottom">
            <div><span class="font-bold">Cliente:</span><br>{{ trim($domicilio->cnombre . ' ' . $domicilio->capellido) }}</div>
            <div><span class="font-bold">Tel:</span> {{ $domicilio->telefono }}</div>
            <div>
                <span class="font-bold">Dirección:</span> {{ $domicilio->calle }}, Col: {{ $domicilio->colonia }}, 
                Ref: {{ $domicilio->referencia }}, Mz: {{ $domicilio->manzana }}, Lt: {{ $domicilio->lote }}
            </div>
        </div>
    @else
        <div class="border-top"></div>
    @endif

    <table class="mb-1">
        <thead>
            <tr>
                <th style="width: 15%;">Cant</th>
                <th style="width: 60%;">Prod</th>
                <th style="width: 25%; text-align: right;">$$</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles as $det)
            <tr>
                <td>{{ $det->cantidad }}</td>
                <td>{{ $det->prod_nombre }}</td>
                <td class="text-right">${{ number_format($det->precio_unitario * $det->cantidad, 2) }}</td>
            </tr>
            @if($det->prod_sub)
            <tr>
                <td></td>
                <td colspan="2" class="sub-item">{!! nl2br(e($det->prod_sub)) !!}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    <div class="border-top mb-1 border-bottom">
        <table>
            <tr>
                <td class="font-bold text-lg">TOTAL:</td>
                <td class="font-bold text-lg text-right">${{ number_format($venta->total, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($venta->comentarios)
        <div class="mb-1 text-center font-bold">
            *** NOTA: {{ $venta->comentarios }} ***
        </div>
    @endif

    <div class="mb-1">
        <div class="font-bold mb-1">FORMA DE PAGO:</div>
        @foreach($pagos as $pago)
            <div>
                @if($pago->id_metpago == 1)
                    Tarjeta: Se enviará terminal.
                @elseif($pago->id_metpago == 2)
                    Efectivo: 
                    @if($pago->referencia && is_numeric($pago->referencia))
                        Pagará con ${{ number_format($pago->referencia, 2) }} (Cambio: ${{ number_format($pago->referencia - $pago->monto, 2) }})
                    @else
                        ${{ number_format($pago->monto, 2) }}
                    @endif
                @elseif($pago->id_metpago == 3)
                    Transferencia: Ya está pagado (Ref: {{ $pago->referencia }})
                @endif
            </div>
        @endforeach
    </div>

    <div class="text-center mt-1 pt-1" style="margin-top: 15px;">
        ¡Gracias por su compra!
    </div>

</body>
</html>