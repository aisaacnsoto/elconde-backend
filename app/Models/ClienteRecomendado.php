<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteRecomendado extends Model
{
    use HasFactory;

    protected $table = 'clientes_recomendados';

    protected $with = ['recomendado'];

    public function recomendado() {
        return $this->belongsTo('App\Models\Cliente', 'recomendado');
    }
}
