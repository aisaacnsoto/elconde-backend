<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoPresentacion extends Model
{
    use HasFactory;

    protected $table = 'productos_presentaciones';
    protected $with = [ 'producto', 'unidad_medida', 'barCodes' ];    

    public function producto() {
        return $this->belongsTo('App\Models\Producto', 'producto_id');
    }

    public function unidad_medida() {
        return $this->belongsTo('App\Models\UnidadMedida', 'unidad_medida_id');
    }
    
    public function barCodes() {
        return $this->hasMany('App\Models\ProductoCodigo', 'producto_id');
    }
}
