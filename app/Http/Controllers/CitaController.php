<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\CitaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return Cita::orderBy('id','desc')->get();
        $sql = "SELECT
                    ci.id,
                    CONCAT(cli.nombres, ' ', cli.apellidos) AS 'cliente',
                    ci.fecha,
                    ci.hora,
                    ci.hora_termino,
                    ci.estado
                FROM citas ci
                INNER JOIN clientes cli ON ci.cliente = cli.id";
        return DB::select($sql);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tiempo_promedio = (int)$request->input('tiempo_promedio');
        $cita = new Cita();
        $cita->codigo = $request->input('codigo');
        $cita->cliente = $request->input('cliente');
        $cita->empleado = $request->input('empleado');

        // Calcular hora
        $fecha = $request->input('fecha');
        $cita->fecha = $fecha;
        // $ultima_cita = Cita::where('fecha', $fecha)->orderBy('id', 'desc')->first();
        // $hora = '08:00:00';
        // if ($ultima_cita) {
        //     $cita->hora = $ultima_cita->hora_termino;
        // } else {
        //     $cita->hora = $hora;
        // }
        $cita->hora = $request->input('hora');
        $convertedTime = date('H:i:s', strtotime("+$tiempo_promedio minutes", strtotime($cita->hora)));
        $cita->hora_termino = $convertedTime;

        // $cita->hora = $request->input('hora');
        $cita->estado = $request->input('estado');
        $promocion = $request->input('promocion');
        $cita->promocion = $promocion ? $promocion['id'] : null;
        $cita->metodo_pago = 'EFECTIVO';
        $cita->total = $request->input('total');
        $cita->created_by = $request->input('created_by');
        $cita->save();

        $detalles = $request->input('detalles');
        if ($detalles) {
            $cita->detalles = $detalles;
            foreach ($cita->detalles as $detalle) {
                $cita_detalle = new CitaDetalle();
                $cita_detalle->cita = $cita->id;
                $cita_detalle->servicio = $detalle['servicio'];
                $cita_detalle->cantidad = $detalle['cantidad'];
                $cita_detalle->precio = $detalle['precio'];
                $cita_detalle->descuento = $detalle['descuento'];
                $cita_detalle->importe = $detalle['importe'];
                $cita_detalle->save();
            }
        }
        return $cita;
    }

    public function updateDetalle($id) {
        $ultima_cita = Cita::where('fecha', date('2020-06-16'))->orderBy('id', 'desc')->first();
        // echo $ultima_cita->hora;

        $hora_convertir = 'time()';
        if ($ultima_cita) {
            $hora_convertir = $ultima_cita->fecha.' '.$ultima_cita->hora;
        }
        // print_r($hora_convertir);

        $cita = Cita::find($id);
        $detalles = $cita->getDetalles;

        $total_tiempo = 0;
        $total = 0;

        foreach ($detalles as $detalle) {
            $total += (int) $detalle->importe;
            $total_tiempo += (int) $detalle->getServicio->tiempo_promedio;

        }
        $convertedTime = date('H:i:s', strtotime('+'.$total_tiempo.' minutes', strtotime($hora_convertir)));
        
        // $cita->hora = $convertedTime;
        $cita->hora1 = $convertedTime;
        $cita->total = $total;
        // $cita->update();
        
        return $cita;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Cita::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cita = Cita::find($id);
        $cita->codigo = $request->input('codigo');
        $cita->cliente = $request->input('cliente');
        $cita->empleado = $request->input('empleado');
        $cita->fecha = $request->input('fecha');
        $cita->hora = $request->input('hora');
        $cita->estado = $request->input('estado');
        $promocion = $request->input('promocion');
        $cita->promocion = $promocion ? $promocion['id'] : null;
        $cita->total = $request->input('total');
        $cita->created_by = $request->input('created_by');
        $cita->save();

        DB::table('citas_detalles')->where('cita', $cita->id)->delete();

        $cita->detalles = $request->input('detalles');
        foreach ($cita->detalles as $detalle) {
            $cita_detalle = new CitaDetalle();
            $cita_detalle->cita = $cita->id;
            $cita_detalle->servicio = $detalle['servicio'];
            $cita_detalle->cantidad = $detalle['cantidad'];
            $cita_detalle->precio = $detalle['precio'];
            $cita_detalle->descuento = $detalle['descuento'];
            $cita_detalle->importe = $detalle['importe'];
            $cita_detalle->save();
        }
        return $cita;
    }

    public function updateEstado(Request $request, $id)
    {
        $cita = Cita::find($id);
        $cita->estado = $request->input('estado');
        $cita->metodo_pago = $request->input('metodo_pago');
        $cita->save();

        if ($cita->estado == 'ATENDIDO' && $cita->promocion == 1) {
            DB::update("UPDATE clientes_recomendados SET estado = 'VENCIDO' WHERE recomendador = ?", [$cita->cliente]);
        }
        
        return $cita;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Cita::find($id);
        $model->delete();
        return $model;
    }

    public function reprogram($id, $from, $to) {
        $from = explode(' ', $from, 2);
        $to = explode(' ', $to, 2);

        $cita = Cita::find($id);
        $cita->fecha = $from[0];
        $cita->hora = $from[1];
        $cita->hora_termino = $to[1];
        $cita->save();

        return $cita;
    }
}
