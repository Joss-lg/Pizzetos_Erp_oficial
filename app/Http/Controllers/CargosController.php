<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CargosController extends Controller
{
    public function index()
    {
        // Se corrige a 'Cargos' y 'Permisos' para coincidir con la DB
        $cargos = DB::table('Cargos')
            ->leftJoin('Permisos', 'Cargos.id_ca', '=', 'Permisos.id_cargo')
            ->select('Cargos.id_ca', 'Cargos.nombre', 'Permisos.*')
            ->get();
            
        return view('Cargos.index', compact('cargos'));
    }

    public function create()
    {
        return view('Cargos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        // Se utiliza la tabla 'Cargos'
        $id_ca = DB::table('Cargos')->insertGetId([
            'nombre' => $request->nombre
        ]);

        // Se utiliza la tabla 'Permisos'
        DB::table('Permisos')->insert([
            'id_cargo' => $id_ca,
            'crear_producto' => $request->has('crear_producto') ? 1 : 0,
            'modificar_producto' => $request->has('modificar_producto') ? 1 : 0,
            'eliminar_producto' => $request->has('eliminar_producto') ? 1 : 0,
            'ver_producto' => $request->has('ver_producto') ? 1 : 0,
            
            'crear_empleado' => $request->has('crear_empleado') ? 1 : 0,
            'modificar_empleado' => $request->has('modificar_empleado') ? 1 : 0,
            'eliminar_empleado' => $request->has('eliminar_empleado') ? 1 : 0,
            'ver_empleado' => $request->has('ver_empleado') ? 1 : 0,
            
            'crear_venta' => $request->has('crear_venta') ? 1 : 0,
            'modificar_venta' => $request->has('modificar_venta') ? 1 : 0,
            'eliminar_venta' => $request->has('eliminar_venta') ? 1 : 0,
            'ver_venta' => $request->has('ver_venta') ? 1 : 0,
            
            'crear_recurso' => $request->has('crear_recurso') ? 1 : 0,
            'modificar_recurso' => $request->has('modificar_recurso') ? 1 : 0,
            'eliminar_recurso' => $request->has('eliminar_recurso') ? 1 : 0,
            'ver_recurso' => $request->has('ver_recurso') ? 1 : 0,
        ]);

        return redirect()->route('cargos.index')->with('success', 'Cargo añadido correctamente.');
    }

    public function edit($id)
    {
        $cargo = DB::table('Cargos')
            ->leftJoin('Permisos', 'Cargos.id_ca', '=', 'Permisos.id_cargo')
            ->select('Cargos.id_ca', 'Cargos.nombre', 'Permisos.*')
            ->where('Cargos.id_ca', $id)
            ->first();
            
        return view('Cargos.edit', compact('cargo'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        
        DB::table('Cargos')->where('id_ca', $id)->update([
            'nombre' => $request->nombre
        ]);
        
        $permisosData = [
            'crear_producto' => $request->has('crear_producto') ? 1 : 0,
            'modificar_producto' => $request->has('modificar_producto') ? 1 : 0,
            'eliminar_producto' => $request->has('eliminar_producto') ? 1 : 0,
            'ver_producto' => $request->has('ver_producto') ? 1 : 0,
            
            'crear_empleado' => $request->has('crear_empleado') ? 1 : 0,
            'modificar_empleado' => $request->has('modificar_empleado') ? 1 : 0,
            'eliminar_empleado' => $request->has('eliminar_empleado') ? 1 : 0,
            'ver_empleado' => $request->has('ver_empleado') ? 1 : 0,
            
            'crear_venta' => $request->has('crear_venta') ? 1 : 0,
            'modificar_venta' => $request->has('modificar_venta') ? 1 : 0,
            'eliminar_venta' => $request->has('eliminar_venta') ? 1 : 0,
            'ver_venta' => $request->has('ver_venta') ? 1 : 0,
            
            'crear_recurso' => $request->has('crear_recurso') ? 1 : 0,
            'modificar_recurso' => $request->has('modificar_recurso') ? 1 : 0,
            'eliminar_recurso' => $request->has('eliminar_recurso') ? 1 : 0,
            'ver_recurso' => $request->has('ver_recurso') ? 1 : 0,
        ];

        $existePermiso = DB::table('Permisos')->where('id_cargo', $id)->exists();
        if ($existePermiso) {
            DB::table('Permisos')->where('id_cargo', $id)->update($permisosData);
        } else {
            $permisosData['id_cargo'] = $id;
            DB::table('Permisos')->insert($permisosData);
        }
        
        return redirect()->route('cargos.index')->with('success', 'Cargo actualizado correctamente.');
    }

    public function destroy($id)
    {
        DB::table('Permisos')->where('id_cargo', $id)->delete();
        DB::table('Cargos')->where('id_ca', $id)->delete();
        
        return redirect()->route('cargos.index')->with('success', 'Cargo eliminado correctamente.');
    }
}