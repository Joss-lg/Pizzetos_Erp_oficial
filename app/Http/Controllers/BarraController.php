<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarraController extends Controller
{
    /**
     * Mostrar la lista de productos de la barra.
     * Tablas corregidas: 'Barra', 'Especialidades' y 'CategoriasProd'.
     */
    public function index()
    {
        $barras = DB::table('Barra')
            ->join('Especialidades', 'Barra.id_especialidad', '=', 'Especialidades.id_esp')
            ->join('CategoriasProd', 'Barra.id_cat', '=', 'CategoriasProd.id_cat')
            ->select(
                'Barra.id_barr', 
                'Especialidades.nombre as especialidad', 
                'Barra.precio', 
                'CategoriasProd.descripcion as categoria'
            )
            ->get();

        return view('Barra.index', compact('barras'));
    }

    /**
     * Formulario para añadir un nuevo producto.
     */
    public function create()
    {
        $especialidades = DB::table('Especialidades')->get();
        $categorias = DB::table('CategoriasProd')->get();
        
        return view('Barra.create', compact('especialidades', 'categorias'));
    }

    /**
     * Guardar en la tabla 'Barra'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_especialidad' => 'required|integer',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);

        DB::table('Barra')->insert([
            'id_especialidad' => $request->id_especialidad,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('barra.index')->with('success', 'Producto de Barra añadido correctamente.');
    }

    /**
     * Editar registro usando 'id_barr'.
     */
    public function edit($id)
    {
        $barra = DB::table('Barra')->where('id_barr', $id)->first();
        
        if (!$barra) {
            return redirect()->route('barra.index')->with('error', 'Registro no encontrado.');
        }

        $especialidades = DB::table('Especialidades')->get();
        $categorias = DB::table('CategoriasProd')->get();

        return view('Barra.edit', compact('barra', 'especialidades', 'categorias'));
    }

    /**
     * Actualizar registro en 'Barra'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_especialidad' => 'required|integer',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('Barra')->where('id_barr', $id)->update([
            'id_especialidad' => $request->id_especialidad,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('barra.index')->with('success', 'Producto de Barra actualizado correctamente.');
    }

    /**
     * Eliminar registro de 'Barra'.
     */
    public function destroy($id)
    {
        DB::table('Barra')->where('id_barr', $id)->delete();
        return redirect()->route('barra.index')->with('success', 'Producto de Barra eliminado correctamente.');
    }
}