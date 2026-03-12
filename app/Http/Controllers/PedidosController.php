<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PedidosController extends Controller
{
    public function index()
    {
        $id_sucursal = 1; // <-- FORZAMOS MIRAFLORES PARA CAJA ÚNICA
        
        // Traemos TODOS los pedidos (Mesa, Mostrador, Domicilio) que NO estén Cancelados
        $pedidosRaw = DB::table('Venta')
            ->leftJoin('PDomicilio', 'Venta.id_venta', '=', 'PDomicilio.id_venta')
            ->leftJoin('Clientes', 'PDomicilio.id_clie', '=', 'Clientes.id_clie')
            ->leftJoin('Direcciones', 'PDomicilio.id_dir', '=', 'Direcciones.id_dir')
            ->where('Venta.id_suc', $id_sucursal)
            ->where('Venta.status', '!=', 3) // Solo excluimos cancelados por BD
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