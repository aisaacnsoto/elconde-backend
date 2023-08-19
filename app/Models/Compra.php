<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = "compras";
    protected $with = ['getProveedor', 'detalle'];

    public function getProveedor() {
        return $this->belongsTo('App\Models\Proveedor', 'proveedor');
    }

    public function detalle() {
        return $this->hasMany('App\Models\CompraDetalle', 'compra');
    }
}
