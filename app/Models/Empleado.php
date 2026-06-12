<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Empleado extends Authenticatable 
{
    use HasFactory, Notifiable;

    protected $table = 'Empleados';
    protected $primaryKey = 'id_emp';
    public $timestamps = false; 

    protected $fillable = [
        'nombre',
        'direccion', 
        'telefono',
        'id_ca',    
        'id_suc',    
        'nickName',
        'password',
        'status',
    ];

    protected $hidden = ['password'];

    public function cargo() {
        return $this->belongsTo(Cargo::class, 'id_ca', 'id_ca'); 
    }

    public function sucursal() {
        return $this->belongsTo(Sucursal::class, 'id_suc', 'id_suc');
    }
}