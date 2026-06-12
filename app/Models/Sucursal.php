<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'Sucursal';
    protected $primaryKey = 'id_suc';
    public $timestamps = false;

    protected $fillable = ['nombre', 'direccion', 'telefono'];
}