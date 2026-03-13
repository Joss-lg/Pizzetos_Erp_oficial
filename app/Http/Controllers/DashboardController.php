<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();

        // 1. Ventas Totales de Hoy
        $ventasHoy = DB::table('Venta')
            ->whereDate('fecha', $hoy)
            ->where('status', 1) // Suponiendo 1 es pagada
            ->sum('total');

        // 2. Conteo de Pedidos Pendientes (Monitor)
        $pedidosPendientes = DB::table('Venta')
            ->whereDate('fecha', $hoy)
            ->whereIn('status_p', ['Pendiente', 'En Preparación'])
            ->count();

        // 3. Producto más vendido del mes
        $topProducto = DB::table('detalle_venta')
            ->select('nombre_p', DB::raw('SUM(cantidad) as total_cantidad'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->groupBy('nombre_p')
            ->orderByDesc('total_cantidad')
            ->first();

        // 4. Últimas 5 ventas para la tabla
        $ultimasVentas = DB::table('Venta')
            ->orderByDesc('id_v')
            ->limit(5)
            ->get();

        return view('dashboard', compact('ventasHoy', 'pedidosPendientes', 'topProducto', 'ultimasVentas'));
    }
}