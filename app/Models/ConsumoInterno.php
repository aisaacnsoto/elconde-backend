<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumoInterno extends Model
{
    use HasFactory;

    protected $table = 'consumos_internos';
    protected $with = ['empleado', 'producto'];

    public function empleado() {
        return $this->belongsTo('App\Models\Empleado', 'empleado_id');
    }

    public function producto() {
        return $this->belongsTo('App\Models\ProductoPresentacion', 'producto_id');
    }
}
