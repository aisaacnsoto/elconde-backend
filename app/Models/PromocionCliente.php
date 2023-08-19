<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromocionCliente extends Model
{
    use HasFactory;

    protected $table = 'promociones_clientes';

    protected $with = ['cliente'];

    public function cliente() {
        return $this->belongsTo('App\Models\Cliente', 'cliente');
    }
}
