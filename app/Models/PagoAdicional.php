<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoAdicional extends Model
{
    use HasFactory;

    protected $table = 'pagos_adicionales';
    protected $with = ['tipo'];

    public function tipo() {
        return $this->belongsTo('App\Models\PagoAdicionalTipo', 'tipo');
    }
}
