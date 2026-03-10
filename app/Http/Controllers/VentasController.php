<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentasController extends Controller
{
    public function resume(Request $request)
    {
        // 1. Iniciamos la consulta con los Joins necesarios para obtener nombres de clientes
        $query = DB::table('Venta')
            ->leftJoin('PDomicilio', 'Venta.id_venta', '=', 'PDomicilio.id_venta')
            ->leftJoin('Clientes', 'PDomicilio.id_clie', '=', 'Clientes.id_clie')
            ->select(
                'Venta.*', 
                'Clientes.nombre as cnombre', 
                'Clientes.apellido as capellido'
            )
            ->orderBy('Venta.fecha_hora', 'desc');

        // 2. Manejo de Filtros
        $filtroFecha = $request->input('fecha', 'hoy');
        $filtroEstado = $request->input('estado', 'todos');

        if ($filtroFecha == 'hoy') {
            $query->whereDate('Venta.fecha_hora', Carbon::today());
        } elseif ($filtroFecha == 'semana') {
            $query->whereBetween('Venta.fecha_hora', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($filtroFecha == 'mes') {
            $query->whereMonth('Venta.fecha_hora', Carbon::now()->month)
                  ->whereYear('Venta.fecha_hora', Carbon::now()->year);
        }

        if ($filtroEstado !== 'todos') {
            $query->where('Venta.status', $filtroEstado);
        }

        $ventas = $query->get();

        // 3. Procesamiento de datos para la vista (Evita el Error 500)
        foreach ($ventas as $v) {
            // Calculamos el total de productos de forma dinámica para cada venta
            $v->total_productos = DB::table('DetalleVenta')
                ->where('id_venta', $v->id_venta)
                ->sum('cantidad');
            
            // Creamos el cliente_display para que la vista lo encuentre
            if ($v->tipo_servicio == 1) {
                // Comedor
                $v->cliente_display = "Mesa " . ($v->mesa ?? '?') . " - " . ($v->nombreClie ?? 'Sin Nombre');
            } elseif ($v->tipo_servicio == 2) {
                // Para llevar
                $v->cliente_display = "Mostrador (Para Llevar)";
            } else {
                // Domicilio (usamos los campos del Join)
                $v->cliente_display = trim(($v->cnombre ?? '') . ' ' . ($v->capellido ?? ''));
                if (empty($v->cliente_display)) {
                    $v->cliente_display = "Pedido a Domicilio";
                }
            }
        }

        return view('Ventas.resume', compact('ventas', 'filtroFecha', 'filtroEstado'));
    }
}