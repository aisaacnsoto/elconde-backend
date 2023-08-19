<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    use HasFactory;

    protected $table = 'asignaciones';
    protected $with = ['empleado', 'producto'];

    public function empleado() {
        return $this->belongsTo('App\Models\Empleado', 'empleado');
    }

    public function producto() {
        return $this->belongsTo('App\Models\ProductoPresentacion', 'producto_id');
    }
}
