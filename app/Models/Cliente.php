<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'Clientes';
    protected $primaryKey = 'id_clie';
    public $timestamps = false;

    protected $fillable = ['nombre', 'apellido', 'telefono', 'status'];

    public function direcciones() {
        return $this->hasMany(Direccion::class, 'id_clie', 'id_clie');
    }
}