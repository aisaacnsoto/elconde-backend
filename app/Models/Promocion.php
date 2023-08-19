<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    use HasFactory;

    protected $table = 'promociones';

    protected $with = ['config', 'servicios', 'clientes'];

    public function config() {
        return $this->hasMany('App\Models\PromocionConfig', 'promocion');
    }

    public function servicios() {
        return $this->hasMany('App\Models\PromocionServicio', 'promocion');
    }

    public function clientes() {
        return $this->hasMany('App\Models\PromocionCliente', 'promocion');
    }
}
