<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PuntoVentaController extends Controller
{
    private function getPreciosOrilla() {
        return [
            'chica' => 35.00,
            'mediana' => 40.00,
            'grande' => 45.00,
            'familiar' => 50.00
        ];
    }

    public function index(Request $request)
    {
        $id_sucursal = 1; // <-- FORZAMOS SUCURSAL 1 (MIRAFLORES) PARA CAJA ÚNICA
        $cajaAbierta = DB::table('Caja')->where('status', 1)->where('id_suc', $id_sucursal)->first();

        $pizzas_raw = DB::table('Pizzas')->join('Especialidades', 'Pizzas.id_esp', '=', 'Especialidades.id_esp')->join('TamanosPizza', 'Pizzas.id_tamano', '=', 'TamanosPizza.id_tamañop')->select('Especialidades.nombre', 'TamanosPizza.tamano', 'TamanosPizza.precio', 'Pizzas.id_pizza')->get();
        $pizzas = []; foreach($pizzas_raw as $p) { if(!isset($pizzas[$p->nombre])) $pizzas[$p->nombre] = ['nombre' => $p->nombre, 'tamanos' => []]; $pizzas[$p->nombre]['tamanos'][] = ['id' => $p->id_pizza, 'tamano' => $p->tamano, 'precio' => $p->precio]; }

        $mariscos_raw = DB::table('PizzasMariscos')->join('TamanosPizza', 'PizzasMariscos.id_tamañop', '=', 'TamanosPizza.id_tamañop')->select('PizzasMariscos.nombre', 'TamanosPizza.tamano', 'TamanosPizza.precio', 'PizzasMariscos.id_maris')->get();
        $mariscos = []; foreach($mariscos_raw as $m) { $nom = str_replace('Pizza ', '', $m->nombre); if(!isset($mariscos[$nom])) $mariscos[$nom] = ['nombre' => $nom, 'tamanos' => []]; $mariscos[$nom]['tamanos'][] = ['id' => $m->id_maris, 'tamano' => $m->tamano, 'precio' => $m->precio]; }

        $bebidas_raw = DB::table('Refrescos')->join('TamanosRefrescos', 'Refrescos.id_tamano', '=', 'TamanosRefrescos.id_tamano')->select('Refrescos.id_refresco as id', 'Refrescos.nombre', 'TamanosRefrescos.tamano', 'TamanosRefrescos.precio')->get();
        $bebidas = []; foreach($bebidas_raw as $b) { if(!isset($bebidas[$b->nombre])) $bebidas[$b->nombre] = ['nombre' => $b->nombre, 'cat' => 1, 'opciones' => []]; $bebidas[$b->nombre]['opciones'][] = ['id' => $b->id, 'tamano' => $b->tamano, 'precio' => $b->precio]; }

        $directos = [];
        foreach(DB::table('Rectangular')->join('Especialidades', 'Rectangular.id_esp', '=', 'Especialidades.id_esp')->select('Rectangular.id_rec as id', 'Especialidades.nombre', 'Rectangular.precio')->get() as $r) { $directos[] = ['id' => $r->id, 'col' => 'id_rec', 'nombre' => $r->nombre, 'precio' => $r->precio, 'cat' => 11]; }
        foreach(DB::table('Barra')->join('Especialidades', 'Barra.id_especialidad', '=', 'Especialidades.id_esp')->select('Barra.id_barr as id', 'Especialidades.nombre', 'Barra.precio')->get() as $b) { $directos[] = ['id' => $b->id, 'col' => 'id_barr', 'nombre' => $b->nombre, 'precio' => $b->precio, 'cat' => 10]; }
        foreach(DB::table('Hamburguesas')->get() as $h) { $directos[] = ['id' => $h->id_hamb, 'col' => 'id_hamb', 'nombre' => $h->paquete, 'precio' => $h->precio, 'cat' => 6]; }
        foreach(DB::table('Alitas')->get() as $a) { $directos[] = ['id' => $a->id_alis, 'col' => 'id_alis', 'nombre' => $a->orden, 'precio' => $a->precio, 'cat' => 5]; }
        foreach(DB::table('Costillas')->get() as $c) { $directos[] = ['id' => $c->id_cos, 'col' => 'id_cos', 'nombre' => $c->orden, 'precio' => $c->precio, 'cat' => 7]; }
        foreach(DB::table('Spaguetty')->get() as $s) { $directos[] = ['id' => $s->id_spag, 'col' => 'id_spag', 'nombre' => $s->orden, 'precio' => $s->precio, 'cat' => 9]; }
        foreach(DB::table('OrdenDePapas')->get() as $p) { $directos[] = ['id' => $p->id_papa, 'col' => 'id_papa', 'nombre' => $p->orden, 'precio' => $p->precio, 'cat' => 8]; }

        $paquetes = DB::table('Paquetes')->get();
        $ingredientes = DB::table('Ingredientes')->get();
        
        $tamanos_base = DB::table('TamanosPizza')
            ->whereIn('tamano', ['Chica', 'Mediana', 'Grande', 'Familiar', 'CHICA', 'MEDIANA', 'GRANDE', 'FAMILIAR'])
            ->orWhere('tamano', 'like', '%Especial%')
            ->get();
        
        $especialidades_lista = DB::table('Especialidades')->get();
        $categorias_extras = DB::table('CategoriasProd')->whereNotIn('id_cat', [12, 2, 11, 10, 1])->get();
        $clientes = []; $direcciones = [];
        try { $clientes = DB::table('Clientes')->where('status', 1)->get(); $direcciones = DB::table('Direcciones')->where('status', 1)->get(); } catch (\Exception $e) {}
        $magno_precio = DB::table('Magno')->value('precio') ?? 0;

        $venta_edit = null;
        $cart_preloaded = [];

        if ($request->has('edit')) {
            $venta_edit = DB::table('Venta')->where('id_venta', $request->edit)->first();
            if($venta_edit) {
                $detalles_edit = DB::table('DetalleVenta')->where('id_venta', $venta_edit->id_venta)->get();
                foreach($detalles_edit as $det) {
                    $ing = $det->ingredientes ? json_decode($det->ingredientes) : null;
                    $item = [
                        'uid' => uniqid(), 'qty' => $det->cantidad, 'precioBase' => $ing->p_base ?? $det->precio_unitario, 'precioFinal' => $det->precio_unitario,
                        'orilla_queso' => ($det->queso > 0), 'orillas_qty' => $det->queso, 'precio_orilla' => $ing->p_orilla ?? 0, 'descuentoPromo' => $ing->desc ?? 0,
                        'comentario' => $ing->nota ?? '', 'ingredientes_extra' => $ing->extras ?? [], 'es_pizza' => false, 'is_magno' => false, 'tipo' => 'directo',
                        'col' => '', 'db_id' => null, 'nombre_base' => 'Producto', 'variante' => '',
                        'is_old' => true
                    ];

                    if ($det->id_pizza) {
                        $p = DB::table('Pizzas')->join('Especialidades', 'Pizzas.id_esp', '=', 'Especialidades.id_esp')->join('TamanosPizza', 'Pizzas.id_tamano', '=', 'TamanosPizza.id_tamañop')->where('Pizzas.id_pizza', $det->id_pizza)->first();
                        $item['es_pizza'] = true; $item['tipo'] = 'pizza_normal'; $item['col'] = 'id_pizza'; $item['db_id'] = $det->id_pizza;
                        if($p) { $item['nombre_base'] = "Pizza " . $p->tamano; $item['variante'] = $p->nombre; }
                    } elseif ($det->id_maris) {
                        $m = DB::table('PizzasMariscos')->join('TamanosPizza', 'PizzasMariscos.id_tamañop', '=', 'TamanosPizza.id_tamañop')->where('PizzasMariscos.id_maris', $det->id_maris)->first();
                        $item['es_pizza'] = true; $item['tipo'] = 'pizza_normal'; $item['col'] = 'id_maris'; $item['db_id'] = $det->id_maris;
                        if($m) { $item['nombre_base'] = "Mariscos " . $m->tamano; $item['variante'] = $m->nombre; }
                    } elseif ($det->id_hamb) { $item['col'] = 'id_hamb'; $item['db_id'] = $det->id_hamb; $item['nombre_base'] = DB::table('Hamburguesas')->where('id_hamb', $det->id_hamb)->value('paquete'); }
                    elseif ($det->id_cos) { $item['col'] = 'id_cos'; $item['db_id'] = $det->id_cos; $item['nombre_base'] = DB::table('Costillas')->where('id_cos', $det->id_cos)->value('orden'); }
                    elseif ($det->id_alis) { $item['col'] = 'id_alis'; $item['db_id'] = $det->id_alis; $item['nombre_base'] = DB::table('Alitas')->where('id_alis', $det->id_alis)->value('orden'); }
                    elseif ($det->id_spag) { $item['col'] = 'id_spag'; $item['db_id'] = $det->id_spag; $item['nombre_base'] = DB::table('Spaguetty')->where('id_spag', $det->id_spag)->value('orden'); }
                    elseif ($det->id_papa) { $item['col'] = 'id_papa'; $item['db_id'] = $det->id_papa; $item['nombre_base'] = DB::table('OrdenDePapas')->where('id_papa', $det->id_papa)->value('orden'); }
                    elseif ($det->id_refresco) {
                        $r = DB::table('Refrescos')->join('TamanosRefrescos', 'Refrescos.id_tamano', '=', 'TamanosRefrescos.id_tamano')->where('Refrescos.id_refresco', $det->id_refresco)->first();
                        $item['col'] = 'id_refresco'; $item['db_id'] = $det->id_refresco; if($r) { $item['nombre_base'] = $r->nombre . " " . $r->tamano; }
                    } elseif ($det->id_rec) {
                        $j = json_decode($det->id_rec); $item['col'] = 'id_rec'; $item['db_id'] = $j->id ?? null; $item['nombre_base'] = "Pizza Rectangular";
                        if(isset($j->cuartos)) { $item['cuartos'] = $j->cuartos; $counts = array_count_values((array)$j->cuartos); $parts = []; foreach($counts as $k => $v) { $parts[] = "$v/4 $k"; } $item['variante'] = implode(", ", $parts); }
                    } elseif ($det->id_barr) {
                        $j = json_decode($det->id_barr); $item['col'] = 'id_barr'; $item['db_id'] = $j->id ?? null; $item['nombre_base'] = "Pizza de Barra";
                        if(isset($j->medios)) { $item['medios'] = $j->medios; $counts = array_count_values((array)$j->medios); $parts = []; foreach($counts as $k => $v) { $parts[] = "$v/2 $k"; } $item['variante'] = implode(", ", $parts); }
                    } elseif ($det->id_magno) {
                        $j = json_decode($det->id_magno); $item['col'] = 'id_magno'; $item['is_magno'] = true; $item['nombre_base'] = "Magno";
                        if(isset($j->medios)) { $item['medios'] = $j->medios; $counts = array_count_values((array)$j->medios); $parts = []; foreach($counts as $k => $v) { $parts[] = "$v/2 $k"; } $item['variante'] = implode(" / ", $parts) . "\n• 1 Refresco de 2L"; }
                    } elseif ($det->id_paquete) {
                        $j = json_decode($det->id_paquete); $item['tipo'] = 'paq'; $item['col'] = 'id_paquete'; $item['db_id'] = $j->id ?? null; $item['nombre_base'] = "Paquete " . ($j->id ?? ''); $item['variante'] = $j->variante ?? ''; $item['max_orillas'] = ($j->id == 1) ? 2 : (($j->id == 2) ? 1 : 3);
                    } elseif ($det->pizza_mitad) {
                        $j = json_decode($det->pizza_mitad); $item['tipo'] = 'piz_mitad'; $item['es_pizza'] = true; $item['nombre_base'] = "Mitad y Mitad " . ($j->tamano ?? ''); $item['variante'] = ($j->mitad1 ?? '') . ' / ' . ($j->mitad2 ?? '');
                        $item['mitad1'] = $j->mitad1 ?? ''; $item['mitad2'] = $j->mitad2 ?? ''; $item['tamano'] = $j->tamano ?? '';
                    } elseif (isset($ing->piz_ing_tamano)) {
                        $item['tipo'] = 'piz_ing'; $item['es_pizza'] = true; $item['nombre_base'] = $ing->piz_ing_tamano; $item['variante'] = 'Ings: ' . implode(", ", $ing->extras ?? []);
                    }
                    $cart_preloaded[] = $item;
                }
            }
        }

        return view('Ventas.pos', [
            'cajaAbierta' => $cajaAbierta, 'pizzas' => array_values($pizzas), 'mariscos' => array_values($mariscos),
            'bebidas' => array_values($bebidas), 'directos' => $directos, 'paquetes' => $paquetes, 
            'ingredientes' => $ingredientes, 'tamanos_base' => $tamanos_base, 'especialidades_lista' => $especialidades_lista, 
            'categorias_extras' => $categorias_extras, 'clientes' => $clientes, 'direcciones' => $direcciones,
            'magno_precio' => $magno_precio, 'precios_orilla' => $this->getPreciosOrilla(),
            'venta_edit' => $venta_edit, 'cart_preloaded' => $cart_preloaded
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $id_sucursal = 1; // <-- FORZAMOS SUCURSAL 1 (MIRAFLORES) PARA CAJA ÚNICA
            $cajaAbierta = DB::table('Caja')->where('status', 1)->where('id_suc', $id_sucursal)->first();
            if(!$cajaAbierta) throw new \Exception("No hay caja abierta.");

            $id_clie = $request->id_clie ?? null;
            $id_dir = $request->id_dir ?? null;

            if ($request->has('nuevo_cliente') && is_array($request->nuevo_cliente) && !empty($request->nuevo_cliente['nombre'])) {
                $id_clie = DB::table('Clientes')->insertGetId([
                    'nombre' => $request->nuevo_cliente['nombre'], 
                    'telefono' => $request->nuevo_cliente['telefono'] ?? '', 
                    'status' => 1
                ]);
            }

            if ($request->has('nueva_direccion') && is_array($request->nueva_direccion) && !empty($request->nueva_direccion['calle']) && $id_clie) {
                $id_dir = DB::table('Direcciones')->insertGetId([
                    'id_clie' => $id_clie, 
                    'calle' => $request->nueva_direccion['calle'], 
                    'manzana' => $request->nueva_direccion['manzana'] ?? '',
                    'lote' => $request->nueva_direccion['lote'] ?? '', 
                    'colonia' => $request->nueva_direccion['colonia'] ?? '', 
                    'referencia' => $request->nueva_direccion['referencia'] ?? '', 
                    'status' => 1
                ]);
            }

            $estado_venta = ($request->has('pagos') && count($request->pagos) > 0) ? 1 : 0;
            $nombreClienteMesa = ($request->tipo_servicio == 1) ? $request->nombre_cliente : null;
            $id_venta = $request->id_venta_edit ?? null;

            $nombreCajero = Auth::check() ? (Auth::user()->nickName ?? 'Usuario') : 'Sistema';
            $comentariosFinales = "Atendió: " . $nombreCajero;
            if (!empty($request->comentarios)) {
                $comentariosFinales .= " | " . $request->comentarios;
            }

            if ($id_venta) {
                DB::table('Venta')->where('id_venta', $id_venta)->update([
                    'total' => $request->total, 'tipo_servicio' => $request->tipo_servicio, 'mesa' => $request->mesa, 
                    'nombreClie' => $nombreClienteMesa, 'comentarios' => $comentariosFinales, 'status' => $estado_venta
                ]);
                DB::table('DetalleVenta')->where('id_venta', $id_venta)->delete();
                DB::table('Pago')->where('id_venta', $id_venta)->delete();
                DB::table('PDomicilio')->where('id_venta', $id_venta)->delete();
            } else {
                $id_venta = DB::table('Venta')->insertGetId([
                    'id_suc' => $id_sucursal, 'id_caja' => $cajaAbierta->id_caja, 'total' => $request->total, 'tipo_servicio' => $request->tipo_servicio,
                    'mesa' => $request->mesa, 'nombreClie' => $nombreClienteMesa, 'comentarios' => $comentariosFinales,
                    'status' => $estado_venta, 'fecha_hora' => Carbon::now()
                ]);
            }

            foreach($request->carrito as $item) {
                $qtyOrillas = $item['orillas_qty'] ?? ((isset($item['orilla_queso']) && $item['orilla_queso']) ? $item['qty'] : 0);
                
                $datosInsert = [
                    'id_venta' => $id_venta, 
                    'cantidad' => $item['qty'], 
                    'precio_unitario' => $item['precioFinal'], 
                    'queso' => $qtyOrillas, 
                    'status' => 1
                ];
                
                $extraData = [];
                $extraData['p_base'] = $item['precioBase'] ?? ($item['precioFinal'] ?? 0);
                $extraData['p_orilla'] = $item['precio_orilla'] ?? 0;
                $extraData['desc'] = $item['descuentoPromo'] ?? 0;
                $extraData['is_old'] = $item['is_old'] ?? false;

                if(!empty($item['comentario'])) $extraData['nota'] = $item['comentario'];
                if(!empty($item['ingredientes_extra'])) $extraData['extras'] = $item['ingredientes_extra'];
                
                $col = $item['col'] ?? null;
                
                if ($item['tipo'] == 'paq') { 
                    $datosInsert['id_paquete'] = json_encode(['id' => $item['db_id'], 'variante' => $item['variante']]); 
                } 
                elseif ($item['tipo'] == 'piz_mitad') { 
                    $datosInsert['pizza_mitad'] = json_encode([
                        'mitad1' => $item['mitad1'], 
                        'mitad2' => $item['mitad2'], 
                        'tamano' => $item['tamano']
                    ]); 
                } 
                elseif ($item['tipo'] == 'piz_ing') { 
                    $datosInsert['id_pizza'] = null; 
                    $extraData['piz_ing_tamano'] = $item['nombre_base']; 
                } 
                elseif ($col === 'id_rec') { 
                    $datosInsert['id_rec'] = json_encode(['id' => $item['db_id'], 'cuartos' => $item['cuartos'] ?? []]); 
                } 
                elseif ($col === 'id_barr') { 
                    $datosInsert['id_barr'] = json_encode(['id' => $item['db_id'], 'medios' => $item['medios'] ?? []]); 
                } 
                elseif ($col === 'id_magno') { 
                    $datosInsert['id_magno'] = json_encode(['medios' => $item['medios'] ?? []]); 
                } 
                elseif ($col) { 
                    $datosInsert[$col] = $item['db_id']; 
                }

                if(!empty($extraData)) $datosInsert['ingredientes'] = json_encode($extraData);
                
                DB::table('DetalleVenta')->insert($datosInsert);
            }

            if ($request->has('pagos')) {
                foreach($request->pagos as $pago) {
                    $datosPago = ['id_venta' => $id_venta, 'id_metpago' => $pago['id_metpago'], 'monto' => $pago['monto']];
                    if (isset($pago['referencia'])) $datosPago['referencia'] = $pago['referencia'];
                    if (isset($pago['entregado'])) $datosPago['referencia'] = $pago['entregado']; 
                    DB::table('Pago')->insert($datosPago);
                }
            }

            if ($request->tipo_servicio == 3 && $id_clie && $id_dir) {
                DB::table('PDomicilio')->insert([
                    'id_venta' => $id_venta, 
                    'id_clie' => $id_clie, 
                    'id_dir' => $id_dir
                ]);
            }

            $nuevoClient_resp = null;
            $nuevaDir_resp = null;

            // Si se creó cliente, obtener sus datos
            if (isset($id_clie) && $request->has('nuevo_cliente')) {
                $nuevoClient_resp = DB::table('Clientes')->where('id_clie', $id_clie)->first();
            }
            // Si se creó dirección, obtener sus datos
            if (isset($id_dir) && $request->has('nueva_direccion')) {
                $nuevaDir_resp = DB::table('Direcciones')->where('id_dir', $id_dir)->first();
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'id_venta' => $id_venta,
                'nuevo_cliente' => $nuevoClient_resp,
                'nueva_direccion' => $nuevaDir_resp
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function pagarOrden(Request $request)
    {
        try {
            DB::beginTransaction();
            $id_venta = $request->id_venta;

            DB::table('Venta')->where('id_venta', $id_venta)->update(['status' => 1]); 

            if ($request->has('pagos')) {
                foreach($request->pagos as $pago) {
                    $datosPago = ['id_venta' => $id_venta, 'id_metpago' => $pago['id_metpago'], 'monto' => $pago['monto']];
                    if (isset($pago['referencia'])) $datosPago['referencia'] = $pago['referencia'];
                    if (isset($pago['entregado'])) $datosPago['referencia'] = $pago['entregado'];
                    DB::table('Pago')->insert($datosPago);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'id_venta' => $id_venta]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

   public function ticket(Request $request, $id)
    {
        $venta = DB::table('Venta')->where('id_venta', $id)->first();
        if(!$venta) abort(404);

        $venta->folio_virtual = Carbon::parse($venta->fecha_hora)->format('d-m-y') . ' ' . str_pad($venta->id_venta, 3, '0', STR_PAD_LEFT);

        $comentarios_limpios = [];
        if ($venta->comentarios) {
            $partes = explode('|', $venta->comentarios);
            foreach($partes as $p) {
                $p = trim($p);
                if (!str_contains($p, 'Atendió:') && !str_contains($p, 'ENTREGADO') && !str_contains($p, 'EN CAMINO') && !str_contains($p, 'CANCELADO')) {
                    if(!empty($p)) {
                        $comentarios_limpios[] = $p;
                    }
                }
            }
        }
        $venta->comentarios = count($comentarios_limpios) > 0 ? "Nota: " . implode(" | ", $comentarios_limpios) : null;

        $detalles = DB::table('DetalleVenta')->where('id_venta', $id)->get();
        
        $pizzas_to_pair = [];
        $other_items = [];

        foreach($detalles as $det) {
            $nombre = "Producto";
            $sub = [];
            $ing = $det->ingredientes ? json_decode($det->ingredientes) : null;
            
            if ($request->has('solo_nuevos') && $request->solo_nuevos == 1) {
                if ($ing && isset($ing->is_old) && $ing->is_old == true) continue; 
            }

            $is_pairable = false;
            $clean_name = "";

            if($ing && isset($ing->piz_ing_tamano)) { 
                $nombre = $ing->piz_ing_tamano; 
                $is_pairable = true;
                $clean_name = "PERSONALIZADA";
            }
            elseif($det->id_pizza) {
                $p = DB::table('Pizzas')->join('Especialidades', 'Pizzas.id_esp', '=', 'Especialidades.id_esp')->join('TamanosPizza', 'Pizzas.id_tamano', '=', 'TamanosPizza.id_tamañop')->where('Pizzas.id_pizza', $det->id_pizza)->first();
                if($p) { $nombre = "Pizza " . $p->tamano . " " . $p->nombre; $is_pairable = true; $clean_name = mb_strtoupper($p->nombre); }
            }
            elseif($det->id_maris) {
                $m = DB::table('PizzasMariscos')->join('TamanosPizza', 'PizzasMariscos.id_tamañop', '=', 'TamanosPizza.id_tamañop')->where('PizzasMariscos.id_maris', $det->id_maris)->first();
                if($m) { $nombre = "Pizza Mariscos " . $m->tamano . " " . $m->nombre; $is_pairable = true; $clean_name = mb_strtoupper($m->nombre); }
            }
            elseif($det->id_hamb) { $nombre = DB::table('Hamburguesas')->where('id_hamb', $det->id_hamb)->value('paquete'); }
            elseif($det->id_cos) { $nombre = DB::table('Costillas')->where('id_cos', $det->id_cos)->value('orden'); }
            elseif($det->id_alis) { $nombre = DB::table('Alitas')->where('id_alis', $det->id_alis)->value('orden'); }
            elseif($det->id_spag) { $nombre = DB::table('Spaguetty')->where('id_spag', $det->id_spag)->value('orden'); }
            elseif($det->id_papa) { $nombre = DB::table('OrdenDePapas')->where('id_papa', $det->id_papa)->value('orden'); }
            elseif($det->id_refresco) {
                $r = DB::table('Refrescos')->join('TamanosRefrescos', 'Refrescos.id_tamano', '=', 'TamanosRefrescos.id_tamano')->where('Refrescos.id_refresco', $det->id_refresco)->first();
                if($r) { $nombre = $r->nombre . " " . $r->tamano; }
            }
            elseif($det->id_rec) {
                $j = json_decode($det->id_rec); $nombre = "Pizza Rectangular";
                if(isset($j->cuartos)) { $counts = array_count_values((array)$j->cuartos); $parts = []; foreach($counts as $k => $v) { $parts[] = "$v/4 $k"; } $sub[] = implode(", ", $parts); }
            }
            elseif($det->id_barr) {
                $j = json_decode($det->id_barr); $nombre = "Pizza de Barra";
                if(isset($j->medios)) { $counts = array_count_values((array)$j->medios); $parts = []; foreach($counts as $k => $v) { $parts[] = "$v/2 $k"; } $sub[] = implode(", ", $parts); }
            }
            elseif($det->id_magno) {
                $j = json_decode($det->id_magno); $nombre = "Magno";
                if(isset($j->medios)) { $counts = array_count_values((array)$j->medios); $parts = []; foreach($counts as $k => $v) { $parts[] = "$v/2 $k"; } $sub[] = implode(" / ", $parts); $sub[] = '1 Refresco de 2L'; }
            }
            elseif($det->id_paquete) {
                $j = json_decode($det->id_paquete); $nombre = "Paquete " . ($j->id ?? '');
                if(isset($j->variante)) { $vars = explode("\n", str_replace(" / ", "\n", $j->variante)); foreach($vars as $v) { $sub[] = $v; } }
            }
            elseif($det->pizza_mitad) {
                $j = json_decode($det->pizza_mitad); 
                
                // 1. Que se llame "Pizza" para que el sistema sepa cómo agruparla por tamaño (CHICA, GRANDE, etc)
                $nombre = "Pizza " . ($j->tamano ?? ''); 
                $is_pairable = true;
                
                // 2. Aquí armamos directamente la frase limpia "1/2 ESPECIALIDAD Y 1/2 OTRA"
                $clean_name = mb_strtoupper("1/2 " . ($j->mitad1 ?? '') . " Y 1/2 " . ($j->mitad2 ?? ''));
            }

            if ($det->queso && $det->queso > 0 && !$is_pairable) { 
                $sub[] = ($det->queso > 1 ? $det->queso . ' ' : '') . 'ORILLA RELLENA'; 
            }
            if ($ing && isset($ing->extras) && count($ing->extras) > 0) { 
                $sub[] = 'EXTRAS: ' . implode(", ", $ing->extras); 
            }

            if ($is_pairable) {
                $raw = mb_strtolower($nombre);
                $size = 'MEDIANA';
                if(str_contains($raw, 'chica')) $size = 'CHICA';
                if(str_contains($raw, 'mediana') || str_contains($raw, 'media')) $size = 'MEDIANA';
                if(str_contains($raw, 'grande')) $size = 'GRANDE';
                if(str_contains($raw, 'familiar')) $size = 'FAMILIAR';

                for($i=0; $i<$det->cantidad; $i++) {
                    $pizzas_to_pair[$size][] = [
                        'clean_name' => $clean_name,
                        'precio_final' => $det->precio_unitario,
                        'orilla' => ($det->queso > 0),
                        'subs' => $sub
                    ];
                }
            } else {
                $other_items[] = [
                    'cantidad' => $det->cantidad . 'X',
                    'nombre' => mb_strtoupper($nombre),
                    'total' => $det->precio_unitario * $det->cantidad, 
                    'subs' => $sub
                ];
            }
        }

        $final_items = [];

        foreach ($pizzas_to_pair as $size => $pizzas) {
            $chunks = array_chunk($pizzas, 2);
            foreach($chunks as $chunk) {
                $qty = count($chunk);
                $title = "PIZZA" . ($qty > 1 ? 'S' : '') . " " . $size;
                $total = 0;
                $subs = [];
                
                foreach($chunk as $p) {
                    $total += $p['precio_final'];
                    
                    // 3. Quitamos la estorbosa "1 " que se ponía al principio
                    $desc = $p['clean_name']; 
                    
                    // Si tiene orilla, se la sumamos a la misma línea
                    if($p['orilla']) {
                        $desc .= " + ORILLA RELLENA";
                    }
                    $subs[] = "> " . $desc;
                    
                    // Extras si los hay
                    foreach($p['subs'] as $s) {
                        $subs[] = "  " . mb_strtoupper($s);
                    }
                }

                $final_items[] = (object)[
                    'cantidad' => '',
                    'nombre' => trim($title),
                    'total' => $total,
                    'subs' => $subs
                ];
            }
        }

        foreach ($other_items as $item) {
            $formatted_subs = [];
            foreach($item['subs'] as $s) $formatted_subs[] = "> " . mb_strtoupper($s);

            $final_items[] = (object)[
                'cantidad' => $item['cantidad'],
                'nombre' => $item['nombre'],
                'total' => $item['total'],
                'subs' => $formatted_subs
            ];
        }

        $pagos = DB::table('Pago')->leftJoin('MetodosPago', 'Pago.id_metpago', '=', 'MetodosPago.id_metpago')->where('id_venta', $id)->get();
            
        $domicilio = null;
        if ($venta->tipo_servicio == 3) {
            $domicilio = DB::table('PDomicilio')
                ->join('Clientes', 'PDomicilio.id_clie', '=', 'Clientes.id_clie')
                ->join('Direcciones', 'PDomicilio.id_dir', '=', 'Direcciones.id_dir')
                ->where('PDomicilio.id_venta', $id)
                ->select('Clientes.nombre as cnombre', 'Clientes.apellido as capellido', 'Clientes.telefono', 'Direcciones.*')
                ->first();
        }

        return view('Ventas.ticket', compact('venta', 'final_items', 'pagos', 'domicilio'));
    }
    
    public function historial(Request $request)
    {
        $id_sucursal = 1; 
        
        $ventas = DB::table('Venta')
            ->leftJoin('PDomicilio', 'Venta.id_venta', '=', 'PDomicilio.id_venta')
            ->leftJoin('Clientes', 'PDomicilio.id_clie', '=', 'Clientes.id_clie')
            ->where('Venta.id_suc', $id_sucursal)
            ->orderBy('Venta.fecha_hora', 'desc')
            ->select('Venta.*', 'Clientes.nombre as cnombre', 'Clientes.apellido as capellido')
            ->get();

        foreach ($ventas as $v) {
            $v->total_productos = DB::table('DetalleVenta')->where('id_venta', $v->id_venta)->sum('cantidad');
            if ($v->tipo_servicio == 1) { $v->cliente_display = "Mesa " . $v->mesa . " - " . ($v->nombreClie ?? 'Sin Nombre'); } 
            elseif ($v->tipo_servicio == 2) { $v->cliente_display = "Mostrador (Para Llevar)"; } 
            else { $v->cliente_display = trim(($v->cnombre ?? '') . ' ' . ($v->capellido ?? '')); }
        }

        $filtroFecha = $request->fecha ?? 'todos';
        $filtroEstado = $request->estado ?? 'todos';

        return view('Ventas.historial', compact('ventas', 'filtroFecha', 'filtroEstado'));
    }

    public function cancelarPedido(Request $request)
    {
        try {
            DB::beginTransaction();
            $venta = DB::table('Venta')->where('id_venta', $request->id_venta)->first();
            if(!$venta) {
                return response()->json(['success' => false, 'message' => 'Venta no encontrada']);
            }

            $nuevoComentario = $venta->comentarios . " | CANCELADO - Motivo: " . $request->motivo;

            DB::table('Venta')->where('id_venta', $request->id_venta)->update([
                'status' => 3, 
                'comentarios' => $nuevoComentario
            ]);

            // ELIMINAR LOS PAGOS para que no cuenten en los ingresos (corte de caja)
            DB::table('Pago')->where('id_venta', $request->id_venta)->delete();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function editarPago(Request $request)
    {
        try {
            DB::beginTransaction();
            $id_venta = $request->id_venta;

            DB::table('Pago')->where('id_venta', $id_venta)->delete();

            if ($request->has('pagos')) {
                foreach($request->pagos as $pago) {
                    $datosPago = ['id_venta' => $id_venta, 'id_metpago' => $pago['id_metpago'], 'monto' => $pago['monto']];
                    if (isset($pago['referencia'])) $datosPago['referencia'] = $pago['referencia'];
                    if (isset($pago['entregado'])) $datosPago['referencia'] = $pago['entregado'];
                    DB::table('Pago')->insert($datosPago);
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}