<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    public function citas() {
        return $this->hasMany('App\Models\Cita', 'cliente');
    }

    public function ventas() {
        return $this->hasMany('App\Models\Venta', 'cliente');
    }

    public function opiniones() {
        return $this->hasMany('App\Models\Opinion', 'cliente');
    }

    public function recomendados() {
        return $this->hasMany('App\Models\ClienteRecomendado', 'recomendador');
    }
}
