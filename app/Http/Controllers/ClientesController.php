<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientesController extends Controller
{
    /**
     * Lista todos los clientes activos y sus direcciones.
     */
    public function index()
    {
        // Traemos todos los clientes
        $clientes = DB::table('Clientes')->where('status', 1)->get();
        
        // Consultar todas las direcciones activas
        $direcciones = DB::table('Direcciones')->where('status', 1)->get();
        
        // Agrupar las direcciones por id_clie para que la vista las pueda leer en el modal
        $todasDirecciones = $direcciones->groupBy('id_clie')->toArray();

        return view('Clientes.index', compact('clientes', 'todasDirecciones'));
    }

    /**
     * Muestra el formulario para crear un nuevo cliente.
     */
    public function create()
    {
        return view('Clientes.create');
    }

    /**
     * Guarda el nuevo cliente y su dirección en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20'
        ]);

        try {
            DB::beginTransaction();

            // 1. Insertar el cliente y obtener su ID generado
            $id_clie = DB::table('Clientes')->insertGetId([
                'nombre'   => $request->nombre,
                'apellido' => $request->apellido ?? '',
                'telefono' => $request->telefono,
                'status'   => 1
            ]);

            // 2. Insertar la dirección inicial (si el usuario llenó el campo calle)
            if ($request->filled('calle')) {
                DB::table('Direcciones')->insert([
                    'id_clie'    => $id_clie,
                    'calle'      => $request->calle,
                    'manzana'    => $request->manzana ?? '',
                    'lote'       => $request->lote ?? '',
                    'colonia'    => $request->colonia ?? '',
                    'referencia' => $request->referencia ?? '',
                    'status'     => 1
                ]);
            }

            DB::commit();
            return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el cliente: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit($id)
    {
        // Buscamos por id_clie que es tu llave primaria
        $cliente = DB::table('Clientes')->where('id_clie', $id)->first();
        
        if (!$cliente) {
            return redirect()->route('clientes.index')->with('error', 'Cliente no encontrado');
        }

        // Traemos sus direcciones relacionadas
        $direcciones = DB::table('Direcciones')
            ->where('id_clie', $id)
            ->where('status', 1)
            ->get();
        
        return view('Clientes.edit', compact('cliente', 'direcciones'));
    }

    /**
     * Procesa la actualización del cliente y sus múltiples direcciones.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20'
        ]);

        try {
            DB::beginTransaction();

            // 1. Actualizar datos básicos del cliente
            DB::table('Clientes')->where('id_clie', $id)->update([
                'nombre'   => $request->nombre,
                'apellido' => $request->apellido,
                'telefono' => $request->telefono
            ]);

            // 2. Actualizar cada dirección que se editó en el formulario
            if ($request->has('direcciones')) {
                foreach ($request->direcciones as $id_dir => $dirData) {
                    DB::table('Direcciones')->where('id_dir', $id_dir)->update([
                        'calle'      => $dirData['calle'],
                        'manzana'    => $dirData['manzana'] ?? '',
                        'lote'       => $dirData['lote'] ?? '',
                        'colonia'    => $dirData['colonia'] ?? '',
                        'referencia' => $dirData['referencia'] ?? ''
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('clientes.index')->with('success', 'Cliente y direcciones actualizados correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            // Si hay un error, regresamos con el mensaje exacto para saber qué pasó
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Realiza un borrado lógico del cliente.
     */
    public function destroy($id)
    {
        try {
            // Borrado lógico cambiando status a 0
            DB::table('Clientes')->where('id_clie', $id)->update(['status' => 0]);
            
            return redirect()->route('clientes.index')->with('success', 'Cliente desactivado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo desactivar el cliente.');
        }
    }

    /**
     * Activa un cliente previamente desactivado.
     */
    public function activar($id)
    {
        try {
            DB::table('Clientes')->where('id_clie', $id)->update(['status' => 1]);
            return redirect()->route('clientes.index')->with('success', 'Cliente activado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo activar el cliente.');
        }
    }

    /**
     * Guarda una nueva dirección para un cliente específico desde el modal.
     */
    public function storeDireccion(Request $request, $id)
    {
        $request->validate([
            'calle' => 'required|string|max:255'
        ]);

        try {
            DB::table('Direcciones')->insert([
                'id_clie'    => $id,
                'calle'      => $request->calle,
                'manzana'    => $request->manzana ?? '',
                'lote'       => $request->lote ?? '',
                'colonia'    => $request->colonia ?? '',
                'referencia' => $request->referencia ?? '',
                'status'     => 1
            ]);

            return back()->with('success', 'Dirección agregada correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al agregar la dirección: ' . $e->getMessage());
        }
    }

    /**
     * Realiza un borrado lógico de una dirección específica desde el modal.
     */
    public function destroyDireccion($id)
    {
        try {
            DB::table('Direcciones')->where('id_dir', $id)->update(['status' => 0]);
            return back()->with('success', 'Dirección eliminada correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la dirección: ' . $e->getMessage());
        }
    }
}