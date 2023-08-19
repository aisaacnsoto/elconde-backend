<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    use HasFactory;

    protected $table = 'ventas_detalles';

    protected $with = ['getProducto'];

    public function getProducto() {
        return $this->belongsTo('App\Models\ProductoPresentacion', 'producto');
    }
}
