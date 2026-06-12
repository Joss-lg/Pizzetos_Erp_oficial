<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'CategoriasProd';
    protected $primaryKey = 'id_cat';
    public $timestamps = false; // Importante porque tu SQL no tiene created_at/updated_at

    protected $fillable = ['descripcion'];

    public function alitas() {
        return $this->hasMany(Alita::class, 'id_cat', 'id_cat');
    }
}