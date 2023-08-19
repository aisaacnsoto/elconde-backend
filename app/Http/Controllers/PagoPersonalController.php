<?php

namespace App\Http\Controllers;

use App\Models\PagoPersonal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoPersonalController extends Controller
{
    public function actualizarPagos(Request $request) {
        $fecha = $request->input('fecha');
        $marcados = $request->input('checked');
        $response = [];

        DB::table('pago_personal')->where('fecha', $fecha)->delete();

        foreach ($marcados as $tmp) {
            $empleado_id = (int) $tmp;

            $model = new PagoPersonal();
            $model->empleado = $empleado_id;
            $model->fecha = $fecha;
            $model->monto = 0.0;
            $model->save();

            $response[] = $model;
        }

        return $response;
    }
}
