<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PedidosController extends Controller
{
    public function index()
    {
        $id_sucursal = Auth::check() ? (Auth::user()->id_suc ?? 1) : 1;

        // Traer pedidos activos (0 = Esperando, 1 = Preparando)
        $ventas = DB::table('Venta')
            ->leftJoin('PDomicilio', 'Venta.id_venta', '=', 'PDomicilio.id_venta')
            ->leftJoin('Clientes', 'PDomicilio.id_clie', '=', 'Clientes.id_clie')
            ->whereIn('Venta.status', [0, 1])
            ->where('Venta.id_suc', $id_sucursal)
            ->orderBy('Venta.fecha_hora', 'asc') // Los más antiguos primero
            ->select('Venta.*', 'Clientes.nombre as cnombre', 'Clientes.apellido as capellido')
            ->get();

        // Procesar cada pedido para la vista de cocina
        foreach ($ventas as $v) {
            
            // 1. Formatear Cliente / Mesa
            if ($v->tipo_servicio == 1) { 
                $v->cliente_display = "Mesa " . $v->mesa . " - " . ($v->nombreClie ?? 'Sin Nombre'); 
            } elseif ($v->tipo_servicio == 2) { 
                $v->cliente_display = "Mostrador (Para Llevar)"; 
            } else { 
                $v->cliente_display = trim(($v->cnombre ?? '') . ' ' . ($v->capellido ?? 'Domicilio')); 
            }

            // 2. Calcular Minutos desde que se creó el pedido en el POS (Fallback por si recargan la página)
            $v->minutos = Carbon::parse($v->fecha_hora)->diffInMinutes(Carbon::now());

            // 3. Desglosar Productos con la misma lógica del ticket
            $detalles = DB::table('DetalleVenta')->where('id_venta', $v->id_venta)->get();
            $items_parsed = [];
            $total_items = 0;

            foreach($detalles as $det) {
                $nombre = "Producto";
                $sub = [];
                $ing = $det->ingredientes ? json_decode($det->ingredientes) : null;

                if($ing && isset($ing->piz_ing_tamano)) { $nombre = $ing->piz_ing_tamano; }
                elseif($det->id_pizza) {
                    $p = DB::table('Pizzas')->join('Especialidades', 'Pizzas.id_esp', '=', 'Especialidades.id_esp')->join('TamanosPizza', 'Pizzas.id_tamano', '=', 'TamanosPizza.id_tamañop')->where('Pizzas.id_pizza', $det->id_pizza)->first();
                    if($p) { $nombre = "Pizza " . $p->tamano . " " . $p->nombre; }
                }
                elseif($det->id_hamb) { $nombre = DB::table('Hamburguesas')->where('id_hamb', $det->id_hamb)->value('paquete'); }
                elseif($det->id_cos) { $nombre = DB::table('Costillas')->where('id_cos', $det->id_cos)->value('orden'); }
                elseif($det->id_alis) { $nombre = DB::table('Alitas')->where('id_alis', $det->id_alis)->value('orden'); }
                elseif($det->id_spag) { $nombre = DB::table('Spaguetty')->where('id_spag', $det->id_spag)->value('orden'); }
                elseif($det->id_papa) { $nombre = DB::table('OrdenDePapas')->where('id_papa', $det->id_papa)->value('orden'); }
                elseif($det->id_maris) {
                    $m = DB::table('PizzasMariscos')->join('TamanosPizza', 'PizzasMariscos.id_tamañop', '=', 'TamanosPizza.id_tamañop')->where('PizzasMariscos.id_maris', $det->id_maris)->first();
                    if($m) { $nombre = "Pizza Mariscos " . $m->tamano . " " . $m->nombre; }
                }
                elseif($det->id_refresco) {
                    $r = DB::table('Refrescos')->join('TamanosRefrescos', 'Refrescos.id_tamano', '=', 'TamanosRefrescos.id_tamano')->where('Refrescos.id_refresco', $det->id_refresco)->first();
                    if($r) { $nombre = $r->nombre . " " . $r->tamano; }
                }
                elseif($det->id_rec) {
                    $j = json_decode($det->id_rec); $nombre = "Pizza Rectangular";
                    if(isset($j->cuartos)) { $counts = array_count_values((array)$j->cuartos); $parts = []; foreach($counts as $k => $val) { $parts[] = "$val/4 $k"; } $sub[] = implode(", ", $parts); }
                }
                elseif($det->id_barr) {
                    $j = json_decode($det->id_barr); $nombre = "Pizza de Barra";
                    if(isset($j->medios)) { $counts = array_count_values((array)$j->medios); $parts = []; foreach($counts as $k => $val) { $parts[] = "$val/2 $k"; } $sub[] = implode(", ", $parts); }
                }
                elseif($det->id_magno) {
                    $j = json_decode($det->id_magno); $nombre = "Magno";
                    if(isset($j->medios)) { $counts = array_count_values((array)$j->medios); $parts = []; foreach($counts as $k => $val) { $parts[] = "$val/2 $k"; } $sub[] = implode(" / ", $parts); }
                }
                elseif($det->id_paquete) {
                    $j = json_decode($det->id_paquete); $nombre = "Paquete " . ($j->id ?? '');
                    if(isset($j->variante)) { $vars = explode("\n", str_replace(" / ", "\n", $j->variante)); foreach($vars as $val) { $sub[] = $val; } }
                }
                elseif($det->pizza_mitad) {
                    $j = json_decode($det->pizza_mitad); $nombre = "Mitades " . ($j->tamano ?? '');
                    $sub[] = '1/2 ' . ($j->mitad1 ?? '') . ', 1/2 ' . ($j->mitad2 ?? '');
                }

                if ($det->queso && $det->queso > 0) { $sub[] = '+ ' . $det->queso . ' Orilla(s) Rellena(s)'; }
                if ($ing && isset($ing->extras) && count($ing->extras) > 0) { $sub[] = '+ Extras: ' . implode(", ", $ing->extras); }
                if ($ing && isset($ing->nota) && $ing->nota != '') { $sub[] = 'NOTA: ' . $ing->nota; }

                $items_parsed[] = [
                    'cantidad' => $det->cantidad,
                    'nombre' => $nombre,
                    'sub' => $sub
                ];
                $total_items += $det->cantidad;
            }

            $v->items = $items_parsed;
            $v->total_items = $total_items;
        }

        return view('Ventas.pedidos', compact('ventas'));
    }

    public function cambiarStatus(Request $request, $id)
    {
        try {
            // Actualiza el status de forma sencilla, sin columnas extra de tiempo
            DB::table('Venta')->where('id_venta', $id)->update(['status' => $request->status]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}