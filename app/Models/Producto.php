<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $with = ['getCategoria','getUnidadMedida'];

    public function getCategoria() {
        return $this->belongsTo('App\Models\ProductoCategoria', 'categoria');
    }

    public function getUnidadMedida() {
        return $this->belongsTo('App\Models\UnidadMedida', 'unidad_medida');
    }
    
    public function presentaciones() {
        return $this->hasMany('App\Models\ProductoPresentacion', 'producto_id');
    }

}
