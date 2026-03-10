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
}