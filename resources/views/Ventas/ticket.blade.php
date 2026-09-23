<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* ============================================================
           ESTILOS DE PANTALLA — animación de impresora térmica
        ============================================================ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #111;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 36px 16px 60px;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        /* ---------- Máquina ---------- */
        .printer-machine {
            width: 280px;
            background: linear-gradient(160deg, #1f1f1f 0%, #171717 100%);
            border-radius: 22px 22px 0 0;
            padding: 22px 24px 20px;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.06),
                0 16px 48px rgba(0,0,0,0.7);
            position: relative;
            z-index: 3;
        }

        .printer-top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .printer-brand {
            color: #fff;
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 0.5px;
            line-height: 1;
        }

        .printer-brand span {
            font-weight: 400;
            color: #e65c00;
        }

        .printer-folio {
            text-align: right;
        }

        .printer-folio-label {
            color: #666;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .printer-folio-num {
            color: #fff;
            font-size: 17px;
            font-weight: 900;
            line-height: 1.1;
        }

        .printer-status-row {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 12px;
            padding: 10px 14px;
        }

        .status-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            background: rgba(245, 158, 11, 0.15);
            border: 1.5px solid rgba(245, 158, 11, 0.4);
            transition: background 0.4s, border-color 0.4s;
        }

        .status-icon.done {
            background: rgba(34, 197, 94, 0.15);
            border-color: rgba(34, 197, 94, 0.4);
        }

        .status-texts { flex: 1; }

        .status-label {
            color: #888;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .status-msg {
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            transition: color 0.3s;
        }

        .status-total {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
        }

        /* ---------- Ranura de salida ---------- */
        .printer-slot {
            width: 280px;
            height: 14px;
            background: #0a0a0a;
            position: relative;
            z-index: 4;
            overflow: hidden;
        }

        /* Franja naranja que simula la cabeza de impresión */
        .printer-head {
            position: absolute;
            top: 5px;
            left: -100%;
            width: 100%;
            height: 3px;
            background: linear-gradient(
                90deg,
                transparent 0%,
                rgba(230, 92, 0, 0.0) 10%,
                rgba(230, 92, 0, 0.9) 50%,
                rgba(255, 160, 0, 0.6) 70%,
                transparent 100%
            );
            opacity: 0;
            border-radius: 2px;
        }

        .printer-head.active {
            opacity: 1;
            animation: headSweep 0.45s linear infinite;
        }

        @keyframes headSweep {
            0%   { left: -100%; }
            100% { left: 100%; }
        }

        /* ---------- Papel ---------- */
        .paper-wrap {
            width: 280px;
            background: #fff;
            border-radius: 0 0 10px 10px;
            overflow: hidden;
            height: 0;               /* empieza en 0, JS lo anima */
            position: relative;
            z-index: 2;
            box-shadow: 0 18px 60px rgba(0,0,0,0.55);
        }

        /* Borde perforado superior del papel */
        .paper-wrap::before {
            content: '';
            display: block;
            height: 6px;
            background: repeating-linear-gradient(
                90deg,
                #eee 0px, #eee 8px,
                #fff 8px, #fff 14px
            );
        }

        /* Contenido real del ticket (estilos originales) */
        .ticket-inner {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #000;
            text-transform: uppercase;
            padding: 10px 14px 20px;
            width: 100%;
        }

        .ticket-inner .text-center { text-align: center; }
        .ticket-inner .text-right  { text-align: right; }
        .ticket-inner .font-bold   { font-weight: bold; }
        .ticket-inner .text-xl     { font-size: 22px; letter-spacing: 1px; }
        .ticket-inner .text-lg     { font-size: 16px; }
        .ticket-inner .mt-1        { margin-top: 5px; }
        .ticket-inner .mb-1        { margin-bottom: 5px; }
        .ticket-inner table        { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .ticket-inner th, .ticket-inner td { text-align: left; vertical-align: top; padding: 3px 0; }
        .ticket-inner th           { border-bottom: 1px dashed #000; font-weight: bold; padding-bottom: 3px; font-size: 13px; }
        .ticket-inner .item-principal { font-size: 18px; font-weight: 900; line-height: 1.2; }
        .ticket-inner .sub-item    { font-size: 16px; font-weight: 900; color: #000; line-height: 1.2; }
        .ticket-inner .precio-text { font-size: 15px; font-weight: bold; }
        .ticket-inner .flex-between{ display: flex; justify-content: space-between; align-items: center; }
        .ticket-inner .ticket-logo { width: 150px; height: auto; margin-bottom: 5px; filter: grayscale(100%) contrast(1.2); }

        /* ---------- Botón de imprimir (aparece al terminar) ---------- */
        .print-btn {
            margin-top: 24px;
            background: #e65c00;
            color: #fff;
            border: none;
            padding: 15px 40px;
            border-radius: 100px;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.5px;
            cursor: pointer;
            opacity: 0;
            transform: translateY(14px);
            transition: opacity 0.5s ease, transform 0.5s ease, background 0.2s;
            pointer-events: none;
            box-shadow: 0 4px 24px rgba(230, 92, 0, 0.4);
            text-transform: uppercase;
        }

        .print-btn.visible {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .print-btn:hover {
            background: #ff6a00;
            box-shadow: 0 6px 32px rgba(230, 92, 0, 0.6);
        }

        .print-btn:active {
            transform: scale(0.97);
        }

        /* ============================================================
           ESTILOS DE IMPRESIÓN — ticket limpio, sin animación
        ============================================================ */
        @media print {
            @page {
                margin: 0;   /* elimina encabezado y pie del navegador */
                size: 80mm auto;
            }

            body {
                background: white;
                display: block;
                padding: 0;
                min-height: auto;
            }

            .printer-machine,
            .printer-slot,
            .print-btn { display: none !important; }

            .paper-wrap {
                width: 230px;
                height: auto !important;   /* anuncia la altura completa */
                box-shadow: none;
                border-radius: 0;
                margin: 0 auto;
            }

            .paper-wrap::before { display: none; }

            .ticket-inner {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

    {{-- ===== Máquina impresora ===== --}}
    <div class="printer-machine">
        <div class="printer-top-row">
            <div class="printer-brand">
                <img src="{{ asset('pizzetos.png') }}" alt="Pizzetos" style="height:28px;width:auto;object-fit:contain;filter:brightness(0) invert(1);vertical-align:middle;">
            </div>
            <div class="printer-folio">
                <div class="printer-folio-label">Folio</div>
                <div class="printer-folio-num">#{{ str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <div class="printer-status-row">
            <div class="status-icon" id="statusIcon">
                {{-- SVG cambiado por JS según estado --}}
                <svg id="iconHourglass" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 22h14"/><path d="M5 2h14"/><path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"/><path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"/></svg>
                <svg id="iconCheck" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display:none"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="status-texts">
                <div class="status-label">Estado</div>
                <div class="status-msg" id="statusMsg">Generando ticket...</div>
            </div>
            <div class="status-total">${{ number_format($venta->total, 2) }}</div>
        </div>
    </div>

    {{-- ===== Ranura + cabeza térmica ===== --}}
    <div class="printer-slot">
        <div class="printer-head" id="printerHead"></div>
    </div>

    {{-- ===== Papel animado ===== --}}
    <div class="paper-wrap" id="paperWrap">
        <div class="ticket-inner">

            @php
                $es_especial = ($venta->tipo_servicio == 4);
                $pespecial   = null;
                $dir_especial = null;
                if ($es_especial) {
                    $pespecial = \Illuminate\Support\Facades\DB::table('PEspeciales')
                        ->leftJoin('Clientes', 'PEspeciales.id_clie', '=', 'Clientes.id_clie')
                        ->select('PEspeciales.*', 'Clientes.nombre as cnombre', 'Clientes.apellido as capellido', 'Clientes.telefono')
                        ->where('id_venta', $venta->id_venta)->first();
                    if ($pespecial && $pespecial->id_dir) {
                        $dir_especial = \Illuminate\Support\Facades\DB::table('Direcciones')
                            ->where('id_dir', $pespecial->id_dir)->first();
                    }
                }
            @endphp

            {{-- Encabezado --}}
            <div class="text-center mb-1">
                <img src="{{ asset('pizzetos.png') }}" alt="Pizzetos Logo" class="ticket-logo">
                <div style="font-size: 12px;">TICKET DE VENTA</div>
                <div class="font-bold mt-1" style="font-size: 16px;">
                    FOLIO: {{ str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) }}
                </div>
                <div style="font-size: 12px;">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y h:i A') }}</div>
                <div class="font-bold text-lg mt-1 mb-1 py-1"
                     style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 5px 0;">
                    @if($venta->tipo_servicio == 1) MESA {{ $venta->mesa }}
                    @elseif($venta->tipo_servicio == 2) PARA LLEVAR
                    @elseif($venta->tipo_servicio == 3) DOMICILIO
                    @elseif($venta->tipo_servicio == 4) PEDIDO ESPECIAL
                    @endif
                </div>
            </div>

            {{-- Datos del cliente / dirección --}}
            @if($es_especial && $pespecial)
                <div class="text-center" style="padding-bottom:5px;margin-bottom:5px;border-bottom:1px dashed #000;">
                    <span class="font-bold" style="font-size:14px;">ENTREGAR EL:</span><br>
                    <span style="font-size:16px;font-weight:900;">{{ \Carbon\Carbon::parse($pespecial->fecha_entrega)->format('d/m/Y - h:i A') }}</span>
                </div>
                <div class="mb-1" style="font-size:12px;line-height:1.4;margin-bottom:5px;">
                    <span class="font-bold" style="font-size:13px;">CLIENTE:</span>
                    {{ mb_strtoupper(trim(($pespecial->cnombre ?? '') . ' ' . ($pespecial->capellido ?? ''))) ?: mb_strtoupper($venta->nombreClie) }}<br>
                    <span class="font-bold">TEL:</span> {{ $pespecial->telefono ?? 'S/N' }}
                    @if($dir_especial)
                        <br><span class="font-bold">DIR:</span> {{ $dir_especial->calle ?? 'S/N' }},
                        <span class="font-bold">COL:</span> {{ $dir_especial->colonia ?? 'S/N' }},
                        <span class="font-bold">MZ:</span> {{ $dir_especial->manzana ?? '-' }},
                        <span class="font-bold">LT:</span> {{ $dir_especial->lote ?? '-' }}
                        @if(isset($dir_especial->referencia) && $dir_especial->referencia)
                            <br><span class="font-bold">REF:</span> {{ $dir_especial->referencia }}
                        @endif
                    @else
                        <br><span class="font-bold" style="font-size:13px;">* PASA A RECOGER *</span>
                    @endif
                </div>
                <div style="border-top:1px dashed #000;margin:5px 0;"></div>

            @elseif($venta->tipo_servicio == 3 && $domicilio)
                <div class="mb-1" style="font-size:12px;line-height:1.4;margin-bottom:5px;">
                    <span class="font-bold" style="font-size:13px;">CLIENTE:</span>
                    {{ trim(($domicilio->cnombre ?? '') . ' ' . ($domicilio->capellido ?? '')) }} |
                    <span class="font-bold">TEL:</span> {{ $domicilio->telefono ?? 'S/N' }} |
                    <span class="font-bold">DIR:</span> {{ $domicilio->calle ?? 'S/N' }},
                    <span class="font-bold">COL:</span> {{ $domicilio->colonia ?? 'S/N' }},
                    <span class="font-bold">MZ:</span> {{ $domicilio->manzana ?? '-' }},
                    <span class="font-bold">LT:</span> {{ $domicilio->lote ?? '-' }}
                    @if(isset($domicilio->referencia) && $domicilio->referencia)
                        | <span class="font-bold">REF:</span> {{ $domicilio->referencia }}
                    @endif
                </div>
                <div style="border-top:1px dashed #000;margin:5px 0;"></div>

            @elseif(($venta->tipo_servicio == 2 || $venta->tipo_servicio == 1) && $venta->nombreClie)
                <div class="mb-1" style="font-size:12px;line-height:1.3;">
                    <span class="font-bold">CLIENTE:</span> {{ mb_strtoupper($venta->nombreClie) }}
                </div>
                <div style="border-top:1px dashed #000;margin:5px 0;"></div>
            @endif

            {{-- Productos --}}
            <table class="mb-1">
                <thead>
                    <tr>
                        <th style="width:75%;padding-left:8px;">DESCRIPCIÓN</th>
                        <th style="width:25%;text-align:right;">IMPORTE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($final_items as $item)
                        <tr class="item-principal">
                            <td style="vertical-align:top;padding-top:8px;padding-left:8px;">{{ $item->nombre }}</td>
                            <td class="text-right precio-text" style="vertical-align:top;padding-top:8px;">
                                @if($item->total !== null) ${{ number_format($item->total, 2) }} @endif
                            </td>
                        </tr>
                        @foreach($item->subs as $sub)
                            <tr class="sub-item">
                                @if(is_array($sub))
                                    <td style="padding-bottom:4px;padding-right:5px;padding-left:8px;">
                                        {!! str_replace(' / ', ' <span style="font-weight:900;font-size:18px;margin:0 4px;">/</span> ', e($sub['texto'])) !!}
                                    </td>
                                    <td class="text-right precio-text" style="padding-bottom:4px;vertical-align:top;">
                                        @if(isset($sub['precio'])) ${{ number_format($sub['precio'], 2) }}
                                        @elseif(isset($sub['precio_ext']) && $sub['precio_ext'] != '') {{ $sub['precio_ext'] }}
                                        @endif
                                    </td>
                                @else
                                    <td colspan="2" style="padding-bottom:4px;padding-left:8px;">
                                        {!! str_replace(' / ', ' <span style="font-weight:900;font-size:18px;margin:0 4px;">/</span> ', e($sub)) !!}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        <tr><td colspan="2" style="height:10px;"></td></tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Comentarios --}}
            @if($venta->comentarios)
                <div style="border-top:1px dashed #000;margin-top:5px;"></div>
                <div class="text-center" style="padding:8px 0;font-size:15px;font-weight:900;">
                    {{ $venta->comentarios }}
                </div>
            @endif

            {{-- Totales y pagos --}}
            @if($venta->status == 0)
                <div class="text-center font-bold"
                     style="border:2px solid #000;padding:5px;margin-top:10px;">
                    CUENTA ABIERTA<br>PENDIENTE DE PAGO
                </div>
            @else
                <div style="border-top:1px dashed #000;margin-top:5px;"></div>
                <div style="padding:5px 0;">
                    <div class="flex-between">
                        <div class="font-bold text-lg">TOTAL DEL PEDIDO:</div>
                        <div class="font-bold text-lg">${{ number_format($venta->total, 2) }}</div>
                    </div>
                </div>

                @php
                    $sumaPagos = collect($pagos)->sum('monto');
                    $restante  = $venta->total - $sumaPagos;
                @endphp

                @if($venta->status == 5)
                    <div style="border-top:1px dashed #000;padding:5px 0;">
                        <div class="flex-between" style="font-size:14px;margin-bottom:2px;">
                            <span>ANTICIPO PAGADO:</span>
                            <span class="font-bold">${{ number_format($sumaPagos, 2) }}</span>
                        </div>
                        <div class="flex-between" style="font-size:16px;margin-top:5px;border-top:1px dashed #000;padding-top:5px;">
                            <span class="font-bold">RESTA POR PAGAR:</span>
                            <span class="font-bold text-xl">${{ number_format($restante, 2) }}</span>
                        </div>
                    </div>
                @endif

                <div style="border-top:1px dashed #000;margin-bottom:8px;"></div>

                <div>
                    <div class="font-bold" style="font-size:13px;margin-bottom:5px;">
                        @if($venta->status == 5) ABONOS REGISTRADOS: @else MÉTODO DE PAGO: @endif
                    </div>
                    @foreach($pagos as $pago)
                        <div style="margin-bottom:5px;">
                            @if($pago->id_metpago == 1)
                                <div class="flex-between font-bold">
                                    <span>TARJETA</span><span>${{ number_format($pago->monto, 2) }}</span>
                                </div>
                            @elseif($pago->id_metpago == 2)
                                <div class="flex-between font-bold">
                                    <span>EFECTIVO</span><span>${{ number_format($pago->monto, 2) }}</span>
                                </div>
                                @if($pago->referencia && is_numeric($pago->referencia) && $pago->referencia > $pago->monto)
                                    <div class="flex-between" style="font-size:12px;color:#333;">
                                        <span>RECIBIDO:</span><span>${{ number_format($pago->referencia, 2) }}</span>
                                    </div>
                                    <div class="flex-between" style="font-size:12px;color:#333;">
                                        <span>CAMBIO:</span><span>${{ number_format($pago->referencia - $pago->monto, 2) }}</span>
                                    </div>
                                @endif
                            @elseif($pago->id_metpago == 3)
                                <div class="flex-between font-bold">
                                    <span>TRANSFERENCIA</span><span>${{ number_format($pago->monto, 2) }}</span>
                                </div>
                                @if($pago->referencia && !is_numeric($pago->referencia))
                                    <div style="font-size:12px;color:#333;">REF: {{ mb_strtoupper($pago->referencia) }}</div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="text-center mt-1 pt-1" style="margin-top:20px;font-size:12px;font-weight:bold;">
                ¡GRACIAS POR SU PREFERENCIA!
            </div>

        </div>{{-- /ticket-inner --}}
    </div>{{-- /paper-wrap --}}

    <button class="print-btn" id="printBtn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px;margin-right:8px"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>Imprimir en térmica
    </button>

    <script>
    (function () {
        const paperWrap    = document.getElementById('paperWrap');
        const head         = document.getElementById('printerHead');
        const statusIcon   = document.getElementById('statusIcon');
        const iconHourglass = document.getElementById('iconHourglass');
        const iconCheck    = document.getElementById('iconCheck');
        const statusMsg    = document.getElementById('statusMsg');
        const printBtn     = document.getElementById('printBtn');

        // Velocidad constante: px por ms — como una impresora térmica real
        // 0.55 px/ms ≈ papel saliendo parejo y legible (~550px en ~1s)
        const PX_PER_MS = 0.55;

        function iniciarImpresion() {
            // Reset visual por si se llama de nuevo
            paperWrap.style.height = '0px';
            iconHourglass.style.display = '';
            iconCheck.style.display     = 'none';
            statusIcon.classList.remove('done');
            statusMsg.textContent = 'Imprimiendo…';
            printBtn.classList.remove('visible');

            requestAnimationFrame(function () {
                const targetHeight = paperWrap.scrollHeight;
                // Duración puramente lineal según el largo del ticket
                const duration = Math.max(targetHeight / PX_PER_MS, 1500);

                head.classList.add('active');

                let startTs = null;

                function step(ts) {
                    if (!startTs) startTs = ts;
                    const elapsed  = ts - startTs;
                    const progress = Math.min(elapsed / duration, 1);

                    // Movimiento LINEAL — velocidad constante como térmica real
                    paperWrap.style.height = Math.round(targetHeight * progress) + 'px';

                    if (progress < 1) {
                        requestAnimationFrame(step);
                    } else {
                        // Fin: el papel queda visible, aparece el botón
                        paperWrap.style.height = 'auto';
                        head.classList.remove('active');

                        iconHourglass.style.display = 'none';
                        iconCheck.style.display     = '';
                        statusIcon.classList.add('done');
                        statusMsg.textContent  = 'Listo para imprimir';

                        printBtn.classList.add('visible');
                    }
                }

                requestAnimationFrame(step);
            });
        }

        // Arrancar tras un breve instante para que el usuario vea la máquina
        window.addEventListener('load', function () {
            setTimeout(iniciarImpresion, 500);
        });

        // El botón lanza el diálogo de impresión de la impresora térmica
        printBtn.addEventListener('click', function () {
            window.print();
        });

    })();
    </script>

</body>
</html>