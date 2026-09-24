<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\Cargo;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{
    public function index()
    {
        // Cargamos las relaciones exactas: 'Cargos' (id_ca) y 'Sucursal' (singular)
        $empleados = Empleado::with(['cargo', 'sucursal'])->get();
        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        $cargos = Cargo::all(); 
        $sucursales = Sucursal::all();
        return view('empleados.create', compact('cargos', 'sucursales'));
    }

    public function store(Request $request)
    {
        // 1. VALIDACIÓN: Eliminamos 'email' y 'apellido' porque no existen en tu SQL.
        // Aseguramos que id_ca e id_suc apunten a las tablas correctas.
        $request->validate([
            'nombre'    => 'required|string|max:255',
            'nickName'  => 'required|string|unique:Empleados,nickName',
            'telefono'  => 'required',
            'id_ca'     => 'required|exists:Cargos,id_ca', 
            'id_suc'    => 'required|exists:Sucursal,id_suc',
            'password'  => 'required|string|min:6',
        ]);
        
        try {
            $empleado = new Empleado();
            $empleado->nombre    = $request->nombre;
            $empleado->direccion = $request->direccion ?? ''; // Existe en tu SQL
            $empleado->nickName  = $request->nickName;
            $empleado->telefono  = $request->telefono;
            $empleado->id_ca     = $request->id_ca; 
            $empleado->id_suc    = $request->id_suc;
            $empleado->status    = 1; 
            $empleado->password  = Hash::make($request->password);
            
            // Guardamos
            $empleado->save();

            // Permisos por defecto para empleados no-administradores
            if ($request->id_ca != 1) {
                $permisosDefault = [
                    // módulo            mostrar crear  editar eliminar gestionar
                    ['modulo' => 'dashboard',      'mostrar' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 1, 'gestionar' => 1],
                    ['modulo' => 'pos',            'mostrar' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 1, 'gestionar' => 1],
                    ['modulo' => 'historial',      'mostrar' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 1, 'gestionar' => 1],
                    ['modulo' => 'especiales',     'mostrar' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 1, 'gestionar' => 1],
                    ['modulo' => 'pedidos',        'mostrar' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 1, 'gestionar' => 1],
                    ['modulo' => 'flujo_caja',     'mostrar' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 1, 'gestionar' => 1],
                    ['modulo' => 'clientes',       'mostrar' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 1, 'gestionar' => 1],
                    ['modulo' => 'gastos',         'mostrar' => 1, 'crear' => 1, 'editar' => 0, 'eliminar' => 0, 'gestionar' => 1],
                    ['modulo' => 'empleados',      'mostrar' => 0, 'crear' => 0, 'editar' => 0, 'eliminar' => 0, 'gestionar' => 0],
                    ['modulo' => 'productos',      'mostrar' => 0, 'crear' => 0, 'editar' => 0, 'eliminar' => 0, 'gestionar' => 0],
                    ['modulo' => 'recursos',       'mostrar' => 0, 'crear' => 0, 'editar' => 0, 'eliminar' => 0, 'gestionar' => 0],
                    ['modulo' => 'caja',           'mostrar' => 0, 'crear' => 0, 'editar' => 0, 'eliminar' => 0, 'gestionar' => 0],
                    ['modulo' => 'configuracion',  'mostrar' => 0, 'crear' => 0, 'editar' => 0, 'eliminar' => 0, 'gestionar' => 0],
                ];

                foreach ($permisosDefault as $p) {
                    \App\Models\EmpleadoPermiso::create(array_merge(['id_emp' => $empleado->id_emp], $p));
                }
            }

            return redirect()->route('empleados.index')->with('success', '¡Empleado registrado correctamente!');
            
        } catch (\Exception $e) {
            // El catch te dirá exactamente qué columna falta si vuelve a fallar
            return back()->withInput()->with('error', 'No se pudo guardar: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $empleado = Empleado::where('id_emp', $id)->firstOrFail();
        $cargos = Cargo::all();
        $sucursales = Sucursal::all();

        return view('empleados.edit', compact('empleado', 'cargos', 'sucursales'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'telefono' => 'required',
            'id_ca'    => 'required|exists:Cargos,id_ca',
            'id_suc'   => 'required|exists:Sucursal,id_suc',
            'nickName' => 'required|string|unique:Empleados,nickName,' . $id . ',id_emp',
            'password' => 'nullable|string|min:6',
        ]);

        try {
            $empleado = Empleado::where('id_emp', $id)->firstOrFail();
            
            $empleado->nombre    = $request->nombre;
            $empleado->direccion = $request->direccion ?? '';
            $empleado->nickName  = $request->nickName;
            $empleado->telefono  = $request->telefono;
            $empleado->id_ca     = $request->id_ca; 
            $empleado->id_suc    = $request->id_suc;

            if ($request->filled('password')) {
                $empleado->password = Hash::make($request->password);
            }

            $empleado->save();

            return redirect()->route('empleados.index')->with('success', 'Empleado actualizado con éxito');
            
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $empleado = Empleado::where('id_emp', $id)->firstOrFail();

            // Eliminar permisos del empleado primero (evita FK constraint)
            \App\Models\EmpleadoPermiso::where('id_emp', $id)->delete();

            $empleado->delete();
            return redirect()->route('empleados.index')->with('success', 'Empleado eliminado del sistema');
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo eliminar el empleado: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $empleado = Empleado::where('id_emp', $id)->firstOrFail();
        $empleado->status = $empleado->status == 1 ? 0 : 1;
        $empleado->save();

        $mensaje = $empleado->status == 1 ? 'Empleado activado' : 'Empleado desactivado';
        return redirect()->route('empleados.index')->with('success', $mensaje);
    }
}