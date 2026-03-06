<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PuntoVentaController extends Controller
{
    public function index()
    {
        $id_sucursal = Auth::check() ? (Auth::user()->id_suc ?? 1) : 1;
        $cajaAbierta = DB::table('Caja')->where('status', 1)->where('id_suc', $id_sucursal)->first();

        $pizzas_raw = DB::table('Pizzas')->join('Especialidades', 'Pizzas.id_esp', '=', 'Especialidades.id_esp')->join('TamanosPizza', 'Pizzas.id_tamano', '=', 'TamanosPizza.id_tamañop')->select('Especialidades.nombre', 'TamanosPizza.tamano', 'TamanosPizza.precio', 'Pizzas.id_pizza')->get();
        $pizzas = [];
        foreach($pizzas_raw as $p) {
            if(!isset($pizzas[$p->nombre])) $pizzas[$p->nombre] = ['nombre' => $p->nombre, 'tamanos' => []];
            $pizzas[$p->nombre]['tamanos'][] = ['id' => $p->id_pizza, 'tamano' => $p->tamano, 'precio' => $p->precio];
        }

        $mariscos_raw = DB::table('PizzasMariscos')->join('TamanosPizza', 'PizzasMariscos.id_tamañop', '=', 'TamanosPizza.id_tamañop')->select('PizzasMariscos.nombre', 'TamanosPizza.tamano', 'TamanosPizza.precio', 'PizzasMariscos.id_maris')->get();
        $mariscos = [];
        foreach($mariscos_raw as $m) {
            $nom = str_replace('Pizza ', '', $m->nombre);
            if(!isset($mariscos[$nom])) $mariscos[$nom] = ['nombre' => $nom, 'tamanos' => []];
            $mariscos[$nom]['tamanos'][] = ['id' => $m->id_maris, 'tamano' => $m->tamano, 'precio' => $m->precio];
        }

        $bebidas_raw = DB::table('Refrescos')->join('TamanosRefrescos', 'Refrescos.id_tamano', '=', 'TamanosRefrescos.id_tamano')->select('Refrescos.id_refresco as id', 'Refrescos.nombre', 'TamanosRefrescos.tamano', 'TamanosRefrescos.precio')->get();
        $bebidas = [];
        foreach($bebidas_raw as $b) {
            if(!isset($bebidas[$b->nombre])) $bebidas[$b->nombre] = ['nombre' => $b->nombre, 'cat' => 1, 'opciones' => []];
            $bebidas[$b->nombre]['opciones'][] = ['id' => $b->id, 'tamano' => $b->tamano, 'precio' => $b->precio];
        }

        $directos = [];
        $rectangular = DB::table('Rectangular')->join('Especialidades', 'Rectangular.id_esp', '=', 'Especialidades.id_esp')->select('Rectangular.id_rec as id', 'Especialidades.nombre', 'Rectangular.precio')->get();
        foreach($rectangular as $r) { $directos[] = ['id' => $r->id, 'col' => 'id_rec', 'nombre' => $r->nombre, 'precio' => $r->precio, 'cat' => 11]; }

        $barra = DB::table('Barra')->join('Especialidades', 'Barra.id_especialidad', '=', 'Especialidades.id_esp')->select('Barra.id_barr as id', 'Especialidades.nombre', 'Barra.precio')->get();
        foreach($barra as $b) { $directos[] = ['id' => $b->id, 'col' => 'id_barr', 'nombre' => $b->nombre, 'precio' => $b->precio, 'cat' => 10]; }

        foreach(DB::table('Hamburguesas')->get() as $h) { $directos[] = ['id' => $h->id_hamb, 'col' => 'id_hamb', 'nombre' => $h->paquete, 'precio' => $h->precio, 'cat' => 6]; }
        foreach(DB::table('Alitas')->get() as $a) { $directos[] = ['id' => $a->id_alis, 'col' => 'id_alis', 'nombre' => $a->orden, 'precio' => $a->precio, 'cat' => 5]; }
        foreach(DB::table('Costillas')->get() as $c) { $directos[] = ['id' => $c->id_cos, 'col' => 'id_cos', 'nombre' => $c->orden, 'precio' => $c->precio, 'cat' => 7]; }
        foreach(DB::table('Spaguetty')->get() as $s) { $directos[] = ['id' => $s->id_spag, 'col' => 'id_spag', 'nombre' => $s->orden, 'precio' => $s->precio, 'cat' => 9]; }
        foreach(DB::table('OrdenDePapas')->get() as $p) { $directos[] = ['id' => $p->id_papa, 'col' => 'id_papa', 'nombre' => $p->orden, 'precio' => $p->precio, 'cat' => 8]; }

        $paquetes = DB::table('Paquetes')->get();
        $ingredientes = DB::table('Ingredientes')->get();
        $tamanos_base = DB::table('TamanosPizza')->where('tamano', 'like', '%Especial%')->get(); 
        $especialidades_lista = DB::table('Especialidades')->get();
        $categorias_extras = DB::table('CategoriasProd')->whereNotIn('id_cat', [12, 2, 11, 10, 1])->get();

        $clientes = [];
        $direcciones = [];
        try {
            $clientes = DB::table('Clientes')->where('status', 1)->get(); 
            $direcciones = DB::table('Direcciones')->where('status', 1)->get(); 
        } catch (\Exception $e) {}

        return view('Ventas.pos', [
            'cajaAbierta' => $cajaAbierta, 'pizzas' => array_values($pizzas), 'mariscos' => array_values($mariscos),
            'bebidas' => array_values($bebidas), 'directos' => $directos, 'paquetes' => $paquetes, 
            'ingredientes' => $ingredientes, 'tamanos_base' => $tamanos_base, 'especialidades_lista' => $especialidades_lista, 
            'categorias_extras' => $categorias_extras, 'clientes' => $clientes, 'direcciones' => $direcciones
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $id_sucursal = Auth::check() ? (Auth::user()->id_suc ?? 1) : 1;
            $cajaAbierta = DB::table('Caja')->where('status', 1)->where('id_suc', $id_sucursal)->first();
            
            if(!$cajaAbierta) throw new \Exception("No hay caja abierta.");

            // LOGICA PARA NUEVOS CLIENTES / DIRECCIONES
            $id_clie = $request->id_clie ?? null;
            $id_dir = $request->id_dir ?? null;

            if ($request->has('nuevo_cliente') && $request->nuevo_cliente) {
                $id_clie = DB::table('Clientes')->insertGetId([
                    'nombre' => $request->nuevo_cliente['nombre'],
                    'telefono' => $request->nuevo_cliente['telefono'] ?? '',
                    'status' => 1
                ]);
            }

            if ($request->has('nueva_direccion') && $request->nueva_direccion && $id_clie) {
                $id_dir = DB::table('Direcciones')->insertGetId([
                    'id_clie' => $id_clie,
                    'calle' => $request->nueva_direccion['calle'] ?? '',
                    'manzana' => $request->nueva_direccion['manzana'] ?? '',
                    'lote' => $request->nueva_direccion['lote'] ?? '',
                    'colonia' => $request->nueva_direccion['colonia'] ?? '',
                    'referencia' => $request->nueva_direccion['referencia'] ?? '',
                    'status' => 1
                ]);
            }

            $id_venta = DB::table('Venta')->insertGetId([
                'id_suc' => $id_sucursal, 'id_caja' => $cajaAbierta->id_caja,
                'total' => $request->total, 'tipo_servicio' => $request->tipo_servicio,
                'mesa' => $request->mesa, 'comentarios' => $request->comentarios,
                'status' => 0, 'fecha_hora' => Carbon::now()
            ]);

            foreach($request->carrito as $item) {
                $datosInsert = [
                    'id_venta' => $id_venta, 'cantidad' => $item['qty'], 'precio_unitario' => $item['precioFinal'],
                    'queso' => (isset($item['orilla_queso']) && $item['orilla_queso']) ? 1 : 0,
                    'status' => 1
                ];

                $extraData = [];
                if(!empty($item['comentario'])) $extraData['nota'] = $item['comentario'];
                if(!empty($item['ingredientes_extra'])) $extraData['extras'] = $item['ingredientes_extra'];
                if(!empty($extraData)) $datosInsert['ingredientes'] = json_encode($extraData);

                $col = $item['col'] ?? null;

                if ($item['tipo'] == 'paq') {
                    $datosInsert['id_paquete'] = json_encode(['id' => $item['db_id'], 'variante' => $item['variante']]);
                } elseif ($item['tipo'] == 'piz_mitad') {
                    $datosInsert['pizza_mitad'] = json_encode(['mitad1' => $item['mitad1'], 'mitad2' => $item['mitad2'], 'tamano' => $item['tamano']]);
                } elseif ($item['tipo'] == 'piz_ing') {
                    $datosInsert['id_pizza'] = $item['db_id']; 
                } elseif ($col === 'id_rec') {
                    $datosInsert['id_rec'] = json_encode(['id' => $item['db_id'], 'cuartos' => $item['cuartos'] ?? []]);
                } elseif ($col === 'id_barr') {
                    $datosInsert['id_barr'] = json_encode(['id' => $item['db_id'], 'medios' => $item['medios'] ?? []]);
                } elseif ($col === 'id_magno') {
                    $datosInsert['id_magno'] = json_encode(['medios' => $item['medios'] ?? []]);
                } elseif ($col) {
                    $datosInsert[$col] = $item['db_id'];
                }

                DB::table('DetalleVenta')->insert($datosInsert);
            }

            if ($request->has('pagos')) {
                foreach($request->pagos as $pago) {
                    $datosPago = ['id_venta' => $id_venta, 'id_metpago' => $pago['id_metpago'], 'monto' => $pago['monto']];
                    if (isset($pago['referencia'])) $datosPago['referencia'] = $pago['referencia'];
                    DB::table('Pago')->insert($datosPago);
                }
            }

            if ($request->tipo_servicio == 3 && $id_clie && $id_dir) {
                DB::table('PDomicilio')->insert(['id_venta' => $id_venta, 'id_clie' => $id_clie, 'id_dir' => $id_dir]);
            }

            DB::commit();
            return response()->json(['success' => true, 'id_venta' => $id_venta]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function ticket($id)
    {
        $venta = DB::table('Venta')->where('id_venta', $id)->first();
        if(!$venta) abort(404);
        $detalles = DB::table('DetalleVenta')->where('id_venta', $id)->get();
        
        $pagos = DB::table('Pago')
            ->leftJoin('MetodosPago', 'Pago.id_metpago', '=', 'MetodosPago.id_metpago')
            ->where('id_venta', $id)->get();
            
        return view('Ventas.ticket', compact('venta', 'detalles', 'pagos'));
    }
}