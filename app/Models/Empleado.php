<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'Empleados';
    protected $primaryKey = 'id_emp';
    public $timestamps = false; 

    protected $fillable = [
        'nombre',
        'apellido',
        'nickName',
        'email',
        'password',
        'id_suc',
        'id_cargo',
        'status',
    ];

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'id_ca'); 
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_suc');
    }
}