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
        // Cargamos las relaciones para no saturar la base de datos
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
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'nickName' => 'required|string|unique:Empleados,nickName',
            'email'    => 'required|email|unique:Empleados,email',
            'telefono' => 'required',
            'id_cargo' => 'required|exists:Cargos,id_cargo', 
            'id_suc'   => 'required|exists:Sucursales,id_suc',
            'password' => 'required|string|min:6',
        ]);
        
        $empleado = new Empleado();
        $empleado->nombre = $request->nombre;
        $empleado->apellido = $request->apellido ?? '';
        $empleado->nickName = $request->nickName;
        $empleado->email = $request->email;
        $empleado->telefono = $request->telefono;
        $empleado->id_cargo = $request->id_cargo;
        $empleado->id_suc = $request->id_suc;
        $empleado->status = 1; // Activo por defecto
        $empleado->password = Hash::make($request->password);
        
        $empleado->save();

        return redirect()->route('empleados.index')->with('success', '¡Empleado registrado correctamente!');
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
            'apellido' => 'nullable|string|max:255',
            'telefono' => 'required',
            'id_cargo' => 'required|exists:Cargos,id_cargo',
            'id_suc'   => 'required|exists:Sucursales,id_suc',
            'nickName' => 'required|string|unique:Empleados,nickName,' . $id . ',id_emp',
            'email'    => 'required|email|unique:Empleados,email,' . $id . ',id_emp',
            'password' => 'nullable|string|min:6',
        ]);

        $empleado = Empleado::where('id_emp', $id)->firstOrFail();
        
        $empleado->nombre = $request->nombre;
        $empleado->apellido = $request->apellido ?? '';
        $empleado->nickName = $request->nickName;
        $empleado->email = $request->email;
        $empleado->telefono = $request->telefono;
        $empleado->id_cargo = $request->id_cargo;
        $empleado->id_suc = $request->id_suc;

        if ($request->filled('password')) {
            $empleado->password = Hash::make($request->password);
        }

        $empleado->save();

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado con éxito');
    }

    public function destroy($id)
    {
        $empleado = Empleado::where('id_emp', $id)->firstOrFail();
        
        // En sistemas de este tipo es mejor desactivar que borrar físicamente
        // pero si deseas borrarlo:
        $empleado->delete();

        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado del sistema');
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