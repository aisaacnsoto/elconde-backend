<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitaDetalle extends Model
{
    use HasFactory;

    protected $table = 'citas_detalles';

    protected $with = ['getServicio'];

    public function getServicio() {
        return $this->belongsTo('App\Models\Servicio', 'servicio');
    }
}
