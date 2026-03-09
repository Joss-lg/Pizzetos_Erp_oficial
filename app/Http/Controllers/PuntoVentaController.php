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

        $magno_precio = DB::table('Magno')->value('precio') ?? 0;
        $precios_orilla = ['chica' => 35.00, 'mediana' => 40.00, 'grande' => 45.00, 'familiar' => 50.00];

        return view('Ventas.pos', [
            'cajaAbierta' => $cajaAbierta, 'pizzas' => array_values($pizzas), 'mariscos' => array_values($mariscos),
            'bebidas' => array_values($bebidas), 'directos' => $directos, 'paquetes' => $paquetes, 
            'ingredientes' => $ingredientes, 'tamanos_base' => $tamanos_base, 'especialidades_lista' => $especialidades_lista, 
            'categorias_extras' => $categorias_extras, 'clientes' => $clientes, 'direcciones' => $direcciones,
            'magno_precio' => $magno_precio, 'precios_orilla' => $precios_orilla
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $id_sucursal = Auth::check() ? (Auth::user()->id_suc ?? 1) : 1;
            $cajaAbierta = DB::table('Caja')->where('status', 1)->where('id_suc', $id_sucursal)->first();
            
            if(!$cajaAbierta) throw new \Exception("No hay caja abierta.");

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
                $qtyOrillas = $item['orillas_qty'] ?? ((isset($item['orilla_queso']) && $item['orilla_queso']) ? 1 : 0);

                $datosInsert = [
                    'id_venta' => $id_venta, 'cantidad' => $item['qty'], 'precio_unitario' => $item['precioFinal'],
                    'queso' => $qtyOrillas,
                    'status' => 1
                ];

                $extraData = [];
                if(!empty($item['comentario'])) $extraData['nota'] = $item['comentario'];
                if(!empty($item['ingredientes_extra'])) $extraData['extras'] = $item['ingredientes_extra'];
                
                $col = $item['col'] ?? null;

                if ($item['tipo'] == 'paq') {
                    $datosInsert['id_paquete'] = json_encode(['id' => $item['db_id'], 'variante' => $item['variante']]);
                } elseif ($item['tipo'] == 'piz_mitad') {
                    $datosInsert['pizza_mitad'] = json_encode(['mitad1' => $item['mitad1'], 'mitad2' => $item['mitad2'], 'tamano' => $item['tamano']]);
                } elseif ($item['tipo'] == 'piz_ing') {
                    // Para evitar errores de llave foranea en Pizzas, la por ingrediente se guarda en el json
                    $datosInsert['id_pizza'] = null; 
                    $extraData['piz_ing_tamano'] = $item['nombre_base']; 
                } elseif ($col === 'id_rec') {
                    $datosInsert['id_rec'] = json_encode(['id' => $item['db_id'], 'cuartos' => $item['cuartos'] ?? []]);
                } elseif ($col === 'id_barr') {
                    $datosInsert['id_barr'] = json_encode(['id' => $item['db_id'], 'medios' => $item['medios'] ?? []]);
                } elseif ($col === 'id_magno') {
                    $datosInsert['id_magno'] = json_encode(['medios' => $item['medios'] ?? []]);
                } elseif ($col) {
                    $datosInsert[$col] = $item['db_id'];
                }

                if(!empty($extraData)) $datosInsert['ingredientes'] = json_encode($extraData);

                DB::table('DetalleVenta')->insert($datosInsert);
            }

            if ($request->has('pagos')) {
                foreach($request->pagos as $pago) {
                    $datosPago = ['id_venta' => $id_venta, 'id_metpago' => $pago['id_metpago'], 'monto' => $pago['monto']];
                    
                    // Si trae referencia (transferencia) o "entregado" (efectivo), lo guardamos en la columna de referencia.
                    if (isset($pago['referencia'])) $datosPago['referencia'] = $pago['referencia'];
                    if (isset($pago['entregado'])) $datosPago['referencia'] = $pago['entregado'];
                    
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
        
        // Enriquecer el ticket traduciendo IDs a texto
        foreach($detalles as $det) {
            $nombre = "Producto";
            $sub = "";

            $ing = $det->ingredientes ? json_decode($det->ingredientes) : null;
            if($ing && isset($ing->piz_ing_tamano)) {
                $nombre = $ing->piz_ing_tamano;
            }
            elseif($det->id_pizza) {
                $p = DB::table('Pizzas')->join('Especialidades', 'Pizzas.id_esp', '=', 'Especialidades.id_esp')->join('TamanosPizza', 'Pizzas.id_tamano', '=', 'TamanosPizza.id_tamañop')->where('Pizzas.id_pizza', $det->id_pizza)->first();
                if($p) { $nombre = "Pizzas " . $p->tamano; $sub = "1 " . $p->nombre; }
            }
            elseif($det->id_hamb) { $nombre = DB::table('Hamburguesas')->where('id_hamb', $det->id_hamb)->value('paquete'); }
            elseif($det->id_cos) { $nombre = DB::table('Costillas')->where('id_cos', $det->id_cos)->value('orden'); }
            elseif($det->id_alis) { $nombre = DB::table('Alitas')->where('id_alis', $det->id_alis)->value('orden'); }
            elseif($det->id_spag) { $nombre = DB::table('Spaguetty')->where('id_spag', $det->id_spag)->value('orden'); }
            elseif($det->id_papa) { $nombre = DB::table('OrdenDePapas')->where('id_papa', $det->id_papa)->value('orden'); }
            elseif($det->id_maris) {
                $m = DB::table('PizzasMariscos')->join('TamanosPizza', 'PizzasMariscos.id_tamañop', '=', 'TamanosPizza.id_tamañop')->where('PizzasMariscos.id_maris', $det->id_maris)->first();
                if($m) { $nombre = "Mariscos " . $m->tamano; $sub = "1 " . $m->nombre; }
            }
            elseif($det->id_refresco) {
                $r = DB::table('Refrescos')->join('TamanosRefrescos', 'Refrescos.id_tamano', '=', 'TamanosRefrescos.id_tamano')->where('Refrescos.id_refresco', $det->id_refresco)->first();
                if($r) { $nombre = $r->nombre . " " . $r->tamano; }
            }
            elseif($det->id_rec) {
                $j = json_decode($det->id_rec);
                $nombre = "Pizza Rectangular";
                if(isset($j->cuartos)) {
                    $counts = array_count_values($j->cuartos);
                    $parts = []; foreach($counts as $k => $v) { $parts[] = "$v/4 $k"; }
                    $sub = implode(", ", $parts);
                }
            }
            elseif($det->id_barr) {
                $j = json_decode($det->id_barr);
                $nombre = "Pizza de Barra";
                if(isset($j->medios)) {
                    $counts = array_count_values($j->medios);
                    $parts = []; foreach($counts as $k => $v) { $parts[] = "$v/2 $k"; }
                    $sub = implode(", ", $parts);
                }
            }
            elseif($det->id_magno) {
                $j = json_decode($det->id_magno);
                $nombre = "Magno";
                if(isset($j->medios)) {
                    $counts = array_count_values($j->medios);
                    $parts = []; foreach($counts as $k => $v) { $parts[] = "$v/2 $k"; }
                    $sub = implode(" / ", $parts) . "\n+ 1 Refresco de 2L";
                }
            }
            elseif($det->id_paquete) {
                $j = json_decode($det->id_paquete);
                $nombre = "Paquete " . ($j->id ?? '');
                $sub = str_replace(" / ", "\n", ($j->variante ?? ''));
            }
            elseif($det->pizza_mitad) {
                $j = json_decode($det->pizza_mitad);
                $nombre = "Mitades " . ($j->tamano ?? '');
                $sub = "1/2 " . ($j->mitad1 ?? '') . ", 1/2 " . ($j->mitad2 ?? '');
            }

            if ($det->queso && $det->queso > 0) {
                $sub .= "\n+ " . $det->queso . " Orilla(s) Rellena(s)";
            }
            if ($ing) {
                if (isset($ing->extras) && count($ing->extras) > 0) {
                    $sub .= "\n+ Ings: " . implode(", ", $ing->extras);
                }
            }

            $det->prod_nombre = $nombre;
            $det->prod_sub = $sub;
        }
        
        $pagos = DB::table('Pago')
            ->leftJoin('MetodosPago', 'Pago.id_metpago', '=', 'MetodosPago.id_metpago')
            ->where('id_venta', $id)->get();
            
        $domicilio = null;
        if ($venta->tipo_servicio == 3) {
            $domicilio = DB::table('PDomicilio')
                ->join('Clientes', 'PDomicilio.id_clie', '=', 'Clientes.id_clie')
                ->join('Direcciones', 'PDomicilio.id_dir', '=', 'Direcciones.id_dir')
                ->where('PDomicilio.id_venta', $id)
                ->select('Clientes.nombre as cnombre', 'Clientes.apellido as capellido', 'Clientes.telefono', 'Direcciones.*')
                ->first();
        }

        return view('Ventas.ticket', compact('venta', 'detalles', 'pagos', 'domicilio'));
    }
}