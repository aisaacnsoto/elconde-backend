<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaCierre extends Model
{
    use HasFactory;

    protected $table = 'caja_cierres';
    protected $with = [
        'usuario'
    ];

    public function usuario() {
        return $this->belongsTo('App\Models\Usuario', 'created_by');
    }
}
