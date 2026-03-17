<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PedidosController extends Controller
{
    public function index()
    {
        $id_sucursal = 1; 
        
        // 1. Buscar la caja que esté abierta actualmente
        $cajaAbierta = DB::table('Caja')
            ->where('status', 1)
            ->where('id_suc', $id_sucursal)
            ->first();

        // 2. Si no hay caja abierta (se cerró turno), regresamos la vista vacía para limpiar el monitor
        if (!$cajaAbierta) {
            return view('Ventas.pedidos', ['pedidos' => []]);
        }

        // 3. Traemos TODOS los pedidos (Mesa, Mostrador, Domicilio) que pertenezcan ÚNICAMENTE a la caja abierta
        $pedidosRaw = DB::table('Venta')
            ->leftJoin('PDomicilio', 'Venta.id_venta', '=', 'PDomicilio.id_venta')
            ->leftJoin('Clientes', 'PDomicilio.id_clie', '=', 'Clientes.id_clie')
            ->leftJoin('Direcciones', 'PDomicilio.id_dir', '=', 'Direcciones.id_dir')
            ->where('Venta.id_suc', $id_sucursal)
            ->where('Venta.id_caja', $cajaAbierta->id_caja) 
            ->where('Venta.status', '!=', 3) 
            ->select(
                'Venta.*', 
                'Clientes.nombre as cnombre', 
                'Clientes.apellido as capellido', 
                'Clientes.telefono', 
                'Direcciones.calle', 
                'Direcciones.manzana', 
                'Direcciones.lote', 
                'Direcciones.colonia', 
                'Direcciones.referencia'
            )
            ->orderBy('Venta.fecha_hora', 'asc') 
            ->get();

        $pedidos = [];
        foreach($pedidosRaw as $p) {
            if ($p->status == 2 || str_contains($p->comentarios ?? '', 'ENTREGADO')) {
                continue;
            }
            $pedidos[] = $p;
        }

        return view('Ventas.pedidos', compact('pedidos'));
    }

    public function cambiarStatus(Request $request, $id)
    {
        $venta = DB::table('Venta')->where('id_venta', $id)->first();
        if(!$venta) return back()->with('error', 'Pedido no encontrado');

        $nuevoComentario = $venta->comentarios;
        $hora = Carbon::now()->format('h:i A');

        // ACCIÓN: ENVIAR CON REPARTIDOR (Solo Domicilio)
        if ($request->accion === 'en_camino') {
            $repartidor = $request->repartidor ?? 'No asignado';
            $nuevoComentario .= " | EN CAMINO ($hora) - Repartidor: $repartidor";
            
            DB::table('Venta')->where('id_venta', $id)->update([
                'comentarios' => $nuevoComentario
            ]);
            return back()->with('success', "Pedido enviado en ruta con: $repartidor");
        }

        // ACCIÓN: MARCAR COMO ENTREGADO / SERVIDO
        if ($request->accion === 'entregado') {
            $nuevoComentario .= " | ENTREGADO ($hora)";
            
            DB::table('Venta')->where('id_venta', $id)->update([
                'comentarios' => $nuevoComentario
            ]);
            return back()->with('success', 'Pedido marcado como completado y retirado del monitor.');
        }

        return back();
    }
}