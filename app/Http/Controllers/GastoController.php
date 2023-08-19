<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GastoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Gasto::where('fecha', date('Y-m-d'))->orderBy('id','desc')->get();
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
        $model = new Gasto();
        $model->fecha = $request->input('fecha');
        $model->nro_comprobante = $request->input('nro_comprobante');
        $model->tipo_comprobante = $request->input('tipo_comprobante');
        $model->tipo_gasto = $request->input('tipo_gasto');
        $model->descripcion = $request->input('descripcion');
        $model->total = $request->input('total');
        $model->created_by = $request->input('created_by');
        $model->save();
        return $model;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $model = Gasto::find($id);
        $model->fecha = $request->input('fecha');
        $model->nro_comprobante = $request->input('nro_comprobante');
        $model->tipo_comprobante = $request->input('tipo_comprobante');
        $model->tipo_gasto = $request->input('tipo_gasto');
        $model->descripcion = $request->input('descripcion');
        $model->total = $request->input('total');
        $model->created_by = $request->input('created_by');
        $model->update();
        return $model;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Gasto::find($id);
        $model->delete();
        return $model;
    }
    
    public function _getMonthName($number) {
        $result = '';

        switch ($number) {
            case 1: $result  = 'Enero'; break;
            case 2: $result  = 'Febrero'; break;
            case 3: $result  = 'Marzo'; break;
            case 4: $result  = 'Abril'; break;
            case 5: $result  = 'Mayo'; break;
            case 6: $result  = 'Junio'; break;
            case 7: $result  = 'Julio'; break;
            case 8: $result  = 'Agosto'; break;
            case 9: $result  = 'Septiembre'; break;
            case 10: $result = 'Octubre'; break;
            case 11: $result = 'Noviembre'; break;
            case 12: $result = 'Diciembre'; break;
        }

        return $result;
    }

}
