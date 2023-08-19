<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    use HasFactory;

    protected $table = 'kardex';

    protected $with = [
        'get_producto'
    ];

    public function get_producto() {
        return $this->belongsTo('App\Models\Producto', 'producto');
    }
}
