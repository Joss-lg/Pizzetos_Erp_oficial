<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoPermiso extends Model
{
    protected $table = 'EmpleadoPermisos';
    protected $primaryKey = 'id_permiso_emp';

    protected $fillable = [
        'id_emp',
        'modulo',
        'mostrar',
        'crear',
        'editar',
        'eliminar',
        'gestionar',
    ];

    protected $casts = [
        'mostrar'   => 'boolean',
        'crear'     => 'boolean',
        'editar'    => 'boolean',
        'eliminar'  => 'boolean',
        'gestionar' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_emp', 'id_emp');
    }
}