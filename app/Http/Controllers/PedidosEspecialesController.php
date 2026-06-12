<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PedidosEspecialesController extends Controller
{
    // --- MOSTRAR LISTA DE PEDIDOS PENDIENTES ---
    public function index()
    {
        $especiales_raw = DB::table('PEspeciales')
            ->join('Venta', 'PEspeciales.id_venta', '=', 'Venta.id_venta')
            ->leftJoin('Clientes', 'PEspeciales.id_clie', '=', 'Clientes.id_clie')
            ->select(
                'PEspeciales.*',
                'Venta.total',
                'Venta.nombreClie as venta_nombre',
                'Venta.comentarios',
                'Venta.tipo_servicio',
                'Clientes.nombre as cnombre',
                'Clientes.apellido as capellido',
                'Clientes.telefono'
            )
            ->where('PEspeciales.status', 1) 
            ->orderBy('PEspeciales.fecha_entrega', 'asc')
            ->get();

        $especiales = [];
        foreach($especiales_raw as $esp) {
            $pagado = DB::table('Pago')->where('id_venta', $esp->id_venta)->sum('monto');
            $esp->pagado = $pagado;
            $esp->restante = $esp->total - $pagado;
            
            $nombre_cliente = trim(($esp->cnombre ?? '') . ' ' . ($esp->capellido ?? ''));
            if(empty($nombre_cliente)) $nombre_cliente = $esp->venta_nombre ?? 'Cliente (POS)';
            
            $esp->cliente_display = $nombre_cliente;
            $esp->telefono_display = $esp->telefono ?? 'N/A';
            $especiales[] = $esp;
        }

        return view('PEspeciales.index', compact('especiales'));
    }

    // --- GUARDAR O ACTUALIZAR EL PEDIDO DESDE EL POS ---
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $id_sucursal = 1; 

            $id_clie = $request->id_clie ?? null;
            $id_dir = $request->id_dir ?? null;

            // REGISTRAR NUEVO CLIENTE/DIRECCION SI SE LLENÓ EL FORMULARIO
            if ($request->has('nuevo_cliente') && is_array($request->nuevo_cliente) && !empty($request->nuevo_cliente['nombre'])) {
                $id_clie = DB::table('Clientes')->insertGetId([
                    'nombre' => $request->nuevo_cliente['nombre'], 
                    'apellido' => $request->nuevo_cliente['apellido'] ?? '', 
                    'telefono' => $request->nuevo_cliente['telefono'] ?? '', 
                    'status' => 1
                ]);
            }
            if ($request->has('nueva_direccion') && is_array($request->nueva_direccion) && !empty($request->nueva_direccion['calle']) && $id_clie) {
                $id_dir = DB::table('Direcciones')->insertGetId([
                    'id_clie' => $id_clie, 'calle' => $request->nueva_direccion['calle'], 
                    'manzana' => $request->nueva_direccion['manzana'] ?? '', 'lote' => $request->nueva_direccion['lote'] ?? '', 
                    'colonia' => $request->nueva_direccion['colonia'] ?? '', 'referencia' => $request->nueva_direccion['referencia'] ?? '', 'status' => 1
                ]);
            }

            $nombreCajero = Auth::check() ? (Auth::user()->nickName ?? 'Usuario') : 'Sistema';
            $comentariosFinales = "Atendió: " . $nombreCajero;
            if (!empty($request->comentarios)) { $comentariosFinales .= " | " . $request->comentarios; }

            $id_venta = $request->id_venta_edit ?? null;

            if ($id_venta) {
                // --- MODO EDICIÓN ---
                DB::table('Venta')->where('id_venta', $id_venta)->update([
                    'total' => $request->total,
                    'nombreClie' => $request->nombre_cliente,
                    'comentarios' => $comentariosFinales
                ]);
                
                DB::table('PEspeciales')->where('id_venta', $id_venta)->update([
                    'id_dir' => $id_dir,
                    'id_clie' => $id_clie,
                    'fecha_entrega' => $request->fecha_entrega,
                    // Sumamos el nuevo anticipo al registro visual de control
                    'anticipo' => DB::raw("anticipo + " . ($request->anticipo ?? 0))
                ]);
                
                // Borramos los detalles viejos para insertar los nuevos del carrito
                DB::table('DetalleVenta')->where('id_venta', $id_venta)->delete();
                // OJO: NO BORRAMOS 'Pago' porque los anticipos ya están físicamente en el corte de caja pasado.
                
            } else {
                // --- MODO CREACIÓN NUEVA ---
                $cajaAbierta = DB::table('Caja')->where('status', 1)->where('id_suc', $id_sucursal)->first();
                if(!$cajaAbierta) throw new \Exception("No hay caja abierta para registrar el anticipo.");
                
                $id_venta = DB::table('Venta')->insertGetId([
                    'id_suc' => $id_sucursal, 'id_caja' => $cajaAbierta->id_caja, 'total' => $request->total, 
                    'tipo_servicio' => 4, 'mesa' => null, 'nombreClie' => $request->nombre_cliente, 
                    'comentarios' => $comentariosFinales, 'status' => 5, 'fecha_hora' => Carbon::now()
                ]);

                DB::table('PEspeciales')->insert([
                    'id_venta' => $id_venta, 'id_dir' => $id_dir, 'id_clie' => $id_clie,
                    'anticipo' => $request->anticipo, 'fecha_creacion' => Carbon::now(),
                    'fecha_entrega' => $request->fecha_entrega, 'status' => 1
                ]);
            }

            // INSERTAR DETALLES DEL CARRITO (Aplica para Nuevo y Edición)
            foreach($request->carrito as $item) {
                $qtyOrillas = $item['orillas_qty'] ?? ((isset($item['orilla_queso']) && $item['orilla_queso']) ? $item['qty'] : 0);
                $datosInsert = ['id_venta' => $id_venta, 'cantidad' => $item['qty'], 'precio_unitario' => $item['precioFinal'], 'queso' => $qtyOrillas, 'status' => 1];
                
                $extraData = [];
                $extraData['p_base'] = $item['precioBase'] ?? ($item['precioFinal'] ?? 0);
                $extraData['p_orilla'] = $item['precio_orilla'] ?? 0;
                $extraData['desc'] = $item['descuentoPromo'] ?? 0;
                $extraData['is_old'] = $item['is_old'] ?? false;

                if(!empty($item['comentario'])) $extraData['nota'] = $item['comentario'];
                if(!empty($item['ingredientes_extra'])) $extraData['extras'] = $item['ingredientes_extra'];
                
                $col = $item['col'] ?? null;
                
                if ($item['tipo'] == 'paq') { $datosInsert['id_paquete'] = json_encode(['id' => $item['db_id'], 'pizzas' => $item['pizzas_paq'] ?? [], 'extra' => $item['extra_paq'] ?? '']); }
                elseif ($item['tipo'] == 'piz_mitad') { $datosInsert['pizza_mitad'] = json_encode(['mitad1' => $item['mitad1'], 'mitad2' => $item['mitad2'], 'tamano' => $item['tamano']]); } 
                elseif ($item['tipo'] == 'piz_ing') { $datosInsert['id_pizza'] = null; $extraData['piz_ing_tamano'] = $item['nombre_base']; } 
                elseif ($col === 'id_rec') { $datosInsert['id_rec'] = json_encode(['id' => $item['db_id'], 'cuartos' => $item['cuartos'] ?? []]); } 
                elseif ($col === 'id_barr') { $datosInsert['id_barr'] = json_encode(['id' => $item['db_id'], 'medios' => $item['medios'] ?? []]); } 
                elseif ($col === 'id_magno') { $datosInsert['id_magno'] = json_encode(['medios' => $item['medios'] ?? []]); } 
                elseif ($col) { $datosInsert[$col] = $item['db_id']; }

                if(!empty($extraData)) $datosInsert['ingredientes'] = json_encode($extraData);
                DB::table('DetalleVenta')->insert($datosInsert);
            }

            // INSERTAR NUEVOS PAGOS (Abonos) (Aplica para Nuevo y Edición si dejan más dinero)
            if ($request->has('pagos_anticipo') && is_array($request->pagos_anticipo)) {
                foreach($request->pagos_anticipo as $pago) {
                    if ($pago['monto'] > 0) {
                        DB::table('Pago')->insert([
                            'id_venta' => $id_venta,
                            'monto' => $pago['monto'],
                            'id_metpago' => $pago['id_metpago'],
                            'referencia' => $pago['referencia'] ?? 'ANTICIPO PEDIDO ESPECIAL'
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pedido especial actualizado', 'id_venta' => $id_venta]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al programar: ' . $e->getMessage()], 500);
        }
    }

    // --- COBRAR RESTANTE Y ENTREGAR ---
    public function marcarEntregado(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            if ($request->monto_cobrado > 0) {
                DB::table('Pago')->insert([
                    'id_venta' => $id, 'monto' => $request->monto_cobrado,
                    'id_metpago' => $request->id_metpago ?? 2, 'referencia' => $request->referencia ?? 'LIQUIDACION'
                ]);
            }
            DB::table('Venta')->where('id_venta', $id)->update(['status' => 1]);
            DB::table('PEspeciales')->where('id_pespeciales', $request->id_pespeciales)->update(['status' => 2]);
            DB::commit();
            return response()->json(['success' => true, 'id_venta' => $id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // --- AGREGAR NUEVO ABONO (MULTIPAGO) DESDE EL PANEL ---
    public function agregarAbono(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            if ($request->has('pagos') && is_array($request->pagos)) {
                foreach($request->pagos as $pago) {
                    if ($pago['monto'] > 0) {
                        DB::table('Pago')->insert([
                            'id_venta' => $id, 'monto' => $pago['monto'],
                            'id_metpago' => $pago['id_metpago'], 'referencia' => $pago['referencia'] ?? 'ABONO PEDIDO ESPECIAL'
                        ]);
                    }
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