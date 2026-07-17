<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $table = 'Empleados';
    protected $primaryKey = 'id_emp';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'direccion', 'telefono', 'id_ca', 'id_suc', 'nickName', 'password', 'status'
    ];

    protected $hidden = ['password'];

    // Relación con el Cargo
    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'id_ca', 'id_ca');
    }

    // Relación con la Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_suc', 'id_suc');
    }

    public function permisos()
    {
        return $this->hasMany(\App\Models\EmpleadoPermiso::class, 'id_emp', 'id_emp');
    }

    /**
     * Un Admin (id_ca = 1) siempre tiene acceso total, sin importar la tabla
     * de permisos. Cualquier otro cargo depende de lo configurado en
     * EmpleadoPermisos para ese módulo/acción específica.
     */
    public function tienePermiso(string $modulo, string $accion): bool
    {
        if ($this->id_ca == 1) {
            return true;
        }

        $permiso = $this->permisos->firstWhere('modulo', $modulo);
        return $permiso ? (bool) $permiso->{$accion} : false;
    }
}