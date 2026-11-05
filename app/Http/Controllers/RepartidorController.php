<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RepartidorController extends Controller
{
    private int $id_sucursal = 1;

    public function index()
    {
        $user = Auth::user();
        $repartidores = $this->getRepartidoresActivos();

        // Vista de repartidor: solo sus pedidos en móvil
        if ($user->id_ca == 3) {
            $pedidos = $this->getPedidosDeRepartidor($user->id_emp);
            return view('Repartidor.mipedidos', compact('pedidos', 'user'));
        }

        // Vista admin/recepcionista: todos los pedidos del día
        $pedidos = $this->getPedidosHoy();
        return view('Repartidor.index', compact('pedidos', 'repartidores'));
    }

    // AJAX: estado actualizado
    public function getEstado()
    {
        $user = Auth::user();

        if ($user->id_ca == 3) {
            return response()->json($this->getPedidosDeRepartidor($user->id_emp));
        }

        return response()->json([
            'pedidos'      => $this->getPedidosHoy(),
            'repartidores' => $this->getRepartidoresActivos(),
        ]);
    }

    // Admin asigna un repartidor a un pedido
    public function asignar(Request $request)
    {
        $request->validate([
            'id_pdomicilio'  => 'required|integer',
            'id_repartidor'  => 'required|integer',
        ]);

        $pedido = DB::table('PDomicilio')->find($request->id_pdomicilio, 'id_pdomicilio');
        if (!$pedido) return response()->json(['error' => 'Pedido no encontrado'], 404);

        DB::table('PDomicilio')->where('id_pdomicilio', $request->id_pdomicilio)->update([
            'id_repartidor' => $request->id_repartidor,
            'status_reparto' => 1, // en camino al asignar
        ]);

        return response()->json(['success' => true]);
    }

    // Paso 1: Repartidor acepta/toma el pedido
    public function tomar(Request $request)
    {
        $user = Auth::user();
        $request->validate(['id_pdomicilio' => 'required|integer']);

        $pedido = DB::table('PDomicilio')->where('id_pdomicilio', $request->id_pdomicilio)->first();
        if (!$pedido) return response()->json(['error' => 'No encontrado'], 404);

        if ($pedido->status_reparto === 0 || $pedido->id_repartidor == $user->id_emp) {
            DB::table('PDomicilio')->where('id_pdomicilio', $request->id_pdomicilio)->update([
                'id_repartidor'  => $user->id_emp,
                'status_reparto' => 1, // tomado
            ]);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Este pedido ya fue tomado por otro repartidor'], 422);
    }

    // Paso 2: Repartidor sale a entregar (en camino)
    public function salir(Request $request)
    {
        $user = Auth::user();
        $request->validate(['id_pdomicilio' => 'required|integer']);

        $q = DB::table('PDomicilio')
            ->where('id_pdomicilio', $request->id_pdomicilio)
            ->where('status_reparto', 1);

        // Repartidor solo puede mover sus propios pedidos; admin/recepcionista puede mover cualquiera
        if ($user->id_ca == 3) {
            $q->where('id_repartidor', $user->id_emp);
        }

        $updated = $q->update(['status_reparto' => 2]); // en camino

        if (!$updated) return response()->json(['error' => 'No se pudo actualizar'], 422);
        return response()->json(['success' => true]);
    }

    // Paso 3: Repartidor entrega al cliente
    public function entregar(Request $request)
    {
        $user = Auth::user();
        $request->validate(['id_pdomicilio' => 'required|integer']);

        // Admin puede marcar cualquiera; repartidor solo los suyos
        $q = DB::table('PDomicilio')
            ->where('id_pdomicilio', $request->id_pdomicilio)
            ->where('status_reparto', 2);

        if ($user->id_ca == 3) {
            $q->where('id_repartidor', $user->id_emp);
        }

        $updated = $q->update(['status_reparto' => 3]); // entregado

        if (!$updated) return response()->json(['error' => 'No se pudo actualizar'], 422);
        return response()->json(['success' => true]);
    }

    // Admin reasigna a otro repartidor
    public function reasignar(Request $request)
    {
        $request->validate([
            'id_pdomicilio' => 'required|integer',
            'id_repartidor' => 'required|integer',
        ]);

        DB::table('PDomicilio')->where('id_pdomicilio', $request->id_pdomicilio)->update([
            'id_repartidor'  => $request->id_repartidor,
            'status_reparto' => 1,
        ]);

        return response()->json(['success' => true]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function getPedidosHoy(): array
    {
        $hoy = Carbon::today()->toDateString();

        return DB::table('PDomicilio as p')
            ->join('Venta as v', 'v.id_venta', '=', 'p.id_venta')
            ->leftJoin('Clientes as c', 'c.id_clie', '=', 'p.id_clie')
            ->leftJoin('Direcciones as d', 'd.id_dir', '=', 'p.id_dir')
            ->leftJoin('Empleados as e', 'e.id_emp', '=', 'p.id_repartidor')
            ->where('v.id_suc', $this->id_sucursal)
            ->whereRaw('DATE(v.fecha_hora) = ?', [$hoy])
            ->select(
                'p.id_pdomicilio', 'p.status_reparto', 'p.id_repartidor',
                'v.id_venta', 'v.total', 'v.fecha_hora', 'v.comentarios',
                'c.nombre as cliente', 'c.telefono',
                'd.calle', 'd.colonia', 'd.referencia', 'd.manzana', 'd.lote',
                'e.nombre as repartidor_nombre'
            )
            ->orderBy('p.status_reparto')
            ->orderBy('v.fecha_hora') // más antiguo primero = orden de llegada
            ->get()
            ->toArray();
    }

    private function getPedidosDeRepartidor(int $id_emp): array
    {
        $hoy = Carbon::today()->toDateString();

        return DB::table('PDomicilio as p')
            ->join('Venta as v', 'v.id_venta', '=', 'p.id_venta')
            ->leftJoin('Clientes as c', 'c.id_clie', '=', 'p.id_clie')
            ->leftJoin('Direcciones as d', 'd.id_dir', '=', 'p.id_dir')
            ->where('v.id_suc', $this->id_sucursal)
            ->whereRaw('DATE(v.fecha_hora) = ?', [$hoy])
            ->where(function ($q) use ($id_emp) {
                $q->where('p.id_repartidor', $id_emp)
                  ->orWhere('p.status_reparto', 0); // sin asignar, cualquiera puede tomar
            })
            ->where('p.status_reparto', '<>', 3) // excluir entregados
            ->select(
                'p.id_pdomicilio', 'p.status_reparto', 'p.id_repartidor',
                'v.id_venta', 'v.total', 'v.fecha_hora',
                'c.nombre as cliente', 'c.telefono',
                'd.calle', 'd.colonia', 'd.referencia', 'd.manzana', 'd.lote'
            )
            ->orderBy('p.status_reparto')
            ->orderBy('v.fecha_hora') // más antiguo primero
            ->get()
            ->toArray();
    }

    private function getRepartidoresActivos(): array
    {
        return DB::table('Empleados')
            ->where('id_ca', 3)
            ->where('status', 1)
            ->select('id_emp', 'nombre', 'nickName')
            ->get()
            ->toArray();
    }
}
