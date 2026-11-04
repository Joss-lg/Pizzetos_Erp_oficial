<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MesasController extends Controller
{
    private int $id_sucursal = 1;

    public function index()
    {
        $mesasOcupadas = $this->getMesasOcupadas();
        $cajaAbierta   = DB::table('Caja')->where('status', 1)->where('id_suc', $this->id_sucursal)->exists();

        return view('Mesas.index', compact('mesasOcupadas', 'cajaAbierta'));
    }

    // AJAX: estado actual de todas las mesas
    public function getEstado()
    {
        return response()->json($this->getMesasOcupadas());
    }

    // AJAX: detalle de una comanda (items)
    public function getDetalle($id_venta)
    {
        $venta = DB::table('Venta')
            ->where('id_venta', $id_venta)
            ->where('id_suc', $this->id_sucursal)
            ->first();

        if (!$venta) return response()->json(['error' => 'No encontrado'], 404);

        $detalles = DB::table('DetalleVenta')->where('id_venta', $id_venta)->where('status', 1)->get();

        $items = [];
        foreach ($detalles as $d) {
            $nombre = $this->resolveNombre($d);
            if ($nombre) {
                $items[] = [
                    'nombre'   => $nombre,
                    'cantidad' => $d->cantidad,
                    'precio'   => $d->precio_unitario,
                ];
            }
        }

        return response()->json([
            'id_venta'    => $venta->id_venta,
            'mesa'        => $venta->mesa,
            'nombreClie'  => $venta->nombreClie,
            'total'       => $venta->total,
            'fecha_hora'  => $venta->fecha_hora,
            'comentarios' => $venta->comentarios,
            'items'       => $items,
        ]);
    }

    // Manual: cierra mesas abiertas de días anteriores
    public function cerrarAnteriores()
    {
        $hoy = \Carbon\Carbon::today()->toDateString();

        $count = \Illuminate\Support\Facades\DB::table('Venta')
            ->where('status', 0)
            ->where('tipo_servicio', 1)
            ->where('id_suc', $this->id_sucursal)
            ->whereNotNull('mesa')
            ->whereRaw('DATE(fecha_hora) < ?', [$hoy])
            ->count();

        if ($count === 0) {
            return response()->json(['success' => true, 'cerradas' => 0, 'message' => 'No hay mesas de días anteriores.']);
        }

        \Illuminate\Support\Facades\DB::table('Venta')
            ->where('status', 0)
            ->where('tipo_servicio', 1)
            ->where('id_suc', $this->id_sucursal)
            ->whereNotNull('mesa')
            ->whereRaw('DATE(fecha_hora) < ?', [$hoy])
            ->update([
                'status'      => 1,
                'comentarios' => \Illuminate\Support\Facades\DB::raw("TRIM(BOTH ' | ' FROM CONCAT(COALESCE(comentarios,''), ' | CIERRE MANUAL " . now()->format('d/m/Y H:i') . "'))"),
            ]);

        return response()->json(['success' => true, 'cerradas' => $count]);
    }

    private function getMesasOcupadas(): array
    {
        $ventas = DB::table('Venta')
            ->where('id_suc', $this->id_sucursal)
            ->where('status', 0)
            ->where('tipo_servicio', 1)
            ->whereNotNull('mesa')
            ->select('id_venta', 'mesa', 'total', 'nombreClie', 'fecha_hora', 'comentarios')
            ->get();

        $map = [];
        foreach ($ventas as $v) {
            $map[(int)$v->mesa] = [
                'id_venta'   => $v->id_venta,
                'total'      => $v->total,
                'nombreClie' => $v->nombreClie,
                'fecha_hora' => $v->fecha_hora,
            ];
        }
        return $map;
    }

    private function resolveNombre($d): ?string
    {
        if ($d->id_pizza) {
            $esp = DB::table('Pizzas')->join('Especialidades', 'Pizzas.id_esp', '=', 'Especialidades.id_esp')
                ->where('Pizzas.id_pizza', $d->id_pizza)->value('Especialidades.nombre');
            return $esp ? "Pizza $esp" : 'Pizza';
        }
        if ($d->id_hamb) {
            return DB::table('Hamburguesas')->where('id_hamb', $d->id_hamb)->value('paquete') ?? 'Hamburguesa';
        }
        if ($d->id_alis) {
            return DB::table('Alitas')->where('id_alis', $d->id_alis)->value('orden') ?? 'Alitas';
        }
        if ($d->id_cos) {
            return DB::table('Costillas')->where('id_cos', $d->id_cos)->value('orden') ?? 'Costillas';
        }
        if ($d->id_spag) {
            return DB::table('Spaguetty')->where('id_spag', $d->id_spag)->value('orden') ?? 'Spaguetti';
        }
        if ($d->id_papa) {
            return DB::table('OrdenDePapas')->where('id_papa', $d->id_papa)->value('orden') ?? 'Papas';
        }
        if ($d->id_refresco) {
            return DB::table('Refrescos')->where('id_refresco', $d->id_refresco)->value('nombre') ?? 'Refresco';
        }
        return null;
    }
}
