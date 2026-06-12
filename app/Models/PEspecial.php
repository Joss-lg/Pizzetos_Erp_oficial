<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PEspecial extends Model
{
    use HasFactory;

    protected $table = 'PEspeciales';

    protected $primaryKey = 'id_pespeciales';

    protected $fillable = [
        'id_venta',
        'id_dir',
        'id_clie',
        'anticipo', 
        'fecha_entrega',
        'status',
    ];


    public $timestamps = false;

    // --- RELACIONES ---


    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_clie', 'id_clie');
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_dir', 'id_dir');
    }
}