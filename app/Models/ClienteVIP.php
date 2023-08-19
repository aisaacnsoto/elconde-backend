<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteVIP extends Model
{
    use HasFactory;

    protected $table = 'clientes_vip';

    protected $with = ['cliente'];

    public function cliente() {
        return $this->belongsTo('App\Models\Cliente', 'cliente');
    }
}
