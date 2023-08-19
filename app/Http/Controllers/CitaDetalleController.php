<?php

namespace App\Http\Controllers;

use App\Models\CitaDetalle;
use Illuminate\Http\Request;

class CitaDetalleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CitaDetalle::all();
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
        $model = new CitaDetalle();
        $model->cita = $request->input('cita');
        $model->servicio = $request->input('servicio');
        $model->cantidad = $request->input('cantidad');
        $model->precio = $request->input('precio');
        $model->importe = $request->input('importe');
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
        $model = CitaDetalle::find($id);
        $model->cita = $request->input('cita');
        $model->servicio = $request->input('servicio');
        $model->cantidad = $request->input('cantidad');
        $model->precio = $request->input('precio');
        $model->importe = $request->input('importe');
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
        $model = CitaDetalle::find($id);
        $model->delete();
        return $model;
    }
}
