<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\EmpleadoPermiso;
use Illuminate\Http\Request;

class PermisosController extends Controller
{
    private const ACCIONES = ['mostrar', 'crear', 'editar', 'eliminar', 'gestionar'];

    public function edit($id)
    {
        $empleado = Empleado::with('permisos')->where('id_emp', $id)->firstOrFail();
        $modulos = config('modulos');

        // Indexamos los permisos existentes por módulo para llenar el formulario.
        $permisosActuales = $empleado->permisos->keyBy('modulo');

        return view('empleados.permisos', [
            'empleado' => $empleado,
            'modulos' => $modulos,
            'permisosActuales' => $permisosActuales,
            'acciones' => self::ACCIONES,
        ]);
    }

    public function update(Request $request, $id)
    {
        $empleado = Empleado::where('id_emp', $id)->firstOrFail();

        // Un Admin siempre tiene acceso total; no tiene caso guardarle restricciones.
        if ($empleado->id_ca == 1) {
            return back()->with('error', 'Los administradores ya tienen acceso total; no se les puede restringir desde aquí.');
        }

        $modulos = array_keys(config('modulos'));
        $enviados = $request->input('permisos', []);

        foreach ($modulos as $modulo) {
            $datosModulo = $enviados[$modulo] ?? [];

            $valores = [];
            foreach (self::ACCIONES as $accion) {
                $valores[$accion] = !empty($datosModulo[$accion]);
            }

            EmpleadoPermiso::updateOrCreate(
                ['id_emp' => $empleado->id_emp, 'modulo' => $modulo],
                $valores
            );
        }

        return redirect()->route('empleados.index')->with('success', 'Permisos actualizados para ' . $empleado->nombre);
    }
}