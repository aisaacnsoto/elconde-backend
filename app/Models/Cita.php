<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $with = [
        'getEmpleado',
        'getCliente',
        'getPromocion',
        'getUsuario',
        'getDetalles'
    ];

    public function getEmpleado() {
        return $this->belongsTo('App\Models\Empleado', 'empleado');
    }

    public function getCliente() {
        return $this->belongsTo('App\Models\Cliente', 'cliente');
    }

    public function getPromocion() {
        return $this->belongsTo('App\Models\Promocion', 'promocion');
    }

    public function getUsuario() {
        return $this->belongsTo('App\Models\Usuario', 'created_by');
    }

    public function getDetalles() {
        return $this->hasMany('App\Models\CitaDetalle', 'cita');
    }
}
