<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opinion extends Model
{
    use HasFactory;

    protected $table = 'opiniones';
    
    public function getEmpleado() {
        return $this->belongsTo('App\Models\Empleado', 'empleado');
    }
}
