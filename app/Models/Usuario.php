<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';

    protected $with = ['getEmpleado', 'getRol', 'permisos'];

    public function getEmpleado() {
        return $this->belongsTo('App\Models\Empleado', 'empleado');
    }

    public function getRol() {
        return $this->belongsTo('App\Models\Rol', 'rol');
    }

    public function permisos() {
        return $this->belongsToMany('App\Models\Permiso', 'usuario_permisos', 'usuario_id', 'permiso_id');
    }
}
