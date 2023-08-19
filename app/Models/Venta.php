<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $with = [
        'getCliente',
        'detalles',
        'getUsuario'
    ];

    public function getCliente() {
        return $this->belongsTo('App\Models\Cliente', 'cliente');
    }

    public function detalles() {
        return $this->hasMany('App\Models\VentaDetalle', 'venta');
    }

    public function getUsuario() {
        return $this->belongsTo('App\Models\Usuario', 'created_by');
    }
}
