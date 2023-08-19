<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    use HasFactory;

    protected $table = 'gastos';

    protected $with = ['getGastoTipo'];

    public function getGastoTipo() {
        return $this->belongsTo('App\Models\GastoTipo', 'tipo_gasto');
    }
}
