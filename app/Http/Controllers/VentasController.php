<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentasController extends Controller
{
    /**
     * Muestra el historial de pedidos con Folios Virtuales (Sin fecha).
     * ACTUALIZADO: Solo muestra las ventas pertenecientes a la CAJA ABIERTA.
     */
   public function resume(Request $request)
{
    $id_sucursal = 1; // SUCURSAL MIRAFLORES

    // Manejo de Filtros
    $filtroFecha = $request->input('fecha', 'hoy');
    $filtroEstado = $request->input('estado', 'todos');

    // 1. Buscar si hay una caja abierta
    $cajaAbierta = DB::table('Caja')
        ->where('status', 1)
        ->where('id_suc', $id_sucursal)
        ->first();

    // 2. Si no hay caja abierta y el filtro es "hoy", regresamos vacío.
    // (Opcional: Si quieres que puedan ver el historial de la semana aunque la caja esté cerrada, 
    // podrías quitar esta validación o ajustarla solo para cuando $filtroFecha == 'hoy')
    if (!$cajaAbierta && $filtroFecha == 'hoy') {
        return view('Ventas.resume', [
            'ventas' => collect([]), 
            'filtroFecha' => $filtroFecha, 
            'filtroEstado' => $filtroEstado
        ]);
    }

    // 3. Construimos la consulta base SIN el filtro de id_caja todavía
    $query = DB::table('Venta')
        ->leftJoin('PDomicilio', 'Venta.id_venta', '=', 'PDomicilio.id_venta')
        ->leftJoin('Clientes', 'PDomicilio.id_clie', '=', 'Clientes.id_clie')
        ->where('Venta.id_suc', $id_sucursal)
        ->select(
            'Venta.*', 
            'Clientes.nombre as cnombre', 
            'Clientes.apellido as capellido'
        )
        ->orderBy('Venta.fecha_hora', 'desc');

    // 4. Aplicamos los filtros de fecha y turno condicionalmente
    if ($filtroFecha == 'hoy') {
        // Si es hoy, limitamos al turno actual (Caja abierta)
        if ($cajaAbierta) {
            $query->where('Venta.id_caja', $cajaAbierta->id_caja);
        } else {
            // Fallback por si acaso (aunque el if de arriba lo previene)
            $query->whereDate('Venta.fecha_hora', Carbon::today());
        }
    } elseif ($filtroFecha == 'semana') {
        // Aseguramos que tome desde el inicio del día 1 hasta el final del último día
        $inicioSemana = Carbon::now()->startOfWeek()->format('Y-m-d 00:00:00');
        $finSemana = Carbon::now()->endOfWeek()->format('Y-m-d 23:59:59');
        
        $query->whereBetween('Venta.fecha_hora', [$inicioSemana, $finSemana]);
    } elseif ($filtroFecha == 'mes') {
        $query->whereMonth('Venta.fecha_hora', Carbon::now()->month)
              ->whereYear('Venta.fecha_hora', Carbon::now()->year);
    }

    // 5. Filtro de Estado
    if ($filtroEstado !== 'todos') {
        $query->where('Venta.status', $filtroEstado);
    }

    $ventas = $query->get();

    // 6. Formateo de datos (N+1 evitado en la medida de lo posible)
    foreach ($ventas as $v) {
        $v->folio_virtual = str_pad($v->id_venta, 5, '0', STR_PAD_LEFT);

        $v->total_productos = DB::table('DetalleVenta')
            ->where('id_venta', $v->id_venta)
            ->sum('cantidad');
        
        if ($v->tipo_servicio == 1) {
            $v->cliente_display = "Mesa " . ($v->mesa ?? '?') . " - " . ($v->nombreClie ?? 'Sin Nombre');
        } elseif ($v->tipo_servicio == 2) {
            $v->cliente_display = "Mostrador (Para Llevar)";
        } else {
            $v->cliente_display = trim(($v->cnombre ?? '') . ' ' . ($v->capellido ?? ''));
            if (empty($v->cliente_display)) $v->cliente_display = "Pedido a Domicilio";
        }
    }

    return view('Ventas.resume', compact('ventas', 'filtroFecha', 'filtroEstado'));
}

    /**
     * Genera la vista del Ticket con el Folio Virtual sin fecha.
     */
    public function ticket($id)
    {
        $venta = DB::table('Venta')->where('id_venta', $id)->first();
        if (!$venta) abort(404);

        // AQUÍ ESTÁ EL TRUCO: Creamos el folio virtual SIN FECHA antes de enviarlo al ticket
        $venta->folio_virtual = str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT);

        $final_items = DB::table('DetalleVenta')
            ->where('id_venta', $id)
            ->select('cantidad', 'nombre', 'total', 'subs')
            ->get()
            ->map(function($item) {
                $item->subs = $item->subs ? explode(',', $item->subs) : [];
                return $item;
            });

        $pagos = DB::table('Pago')->where('id_venta', $id)->get();
        
        $domicilio = DB::table('PDomicilio')
            ->join('Clientes', 'PDomicilio.id_clie', '=', 'Clientes.id_clie')
            ->where('id_venta', $id)
            ->select('Clientes.*', 'PDomicilio.*')
            ->first();

        return view('Ventas.ticket', compact('venta', 'final_items', 'pagos', 'domicilio'));
    }

    /**
     * Procesa la cancelación de una venta.
     */
    public function cancelar(Request $request)
    {
        $id_venta = $request->id_venta;
        $motivo = $request->motivo;
        $usuario = auth()->user()->nombre ?? 'Admin';

        // Marcamos como cancelado y añadimos el motivo a los comentarios
        DB::table('Venta')->where('id_venta', $id_venta)->update([
            'status' => 3,
            'comentarios' => DB::raw("CONCAT(COALESCE(comentarios, ''), ' | CANCELADO - Motivo: $motivo | Por: $usuario')")
        ]);

        return response()->json(['success' => true]);
    }
}